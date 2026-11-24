<?php
declare(strict_types=1);

namespace App\Models;

final class ProductRepository extends Repository
{
    /** A sale applies when inside its window (open-ended if a bound is NULL) and actually cheaper. */
    private const SALE_ACTIVE = '((p2.sale_starts_at IS NULL OR p2.sale_starts_at <= NOW()) AND (p2.sale_ends_at IS NULL OR p2.sale_ends_at > NOW()))';
    private const REGULAR = 'COALESCE(v.price, p2.price)';
    private const SALE = 'COALESCE(v.sale_price, p2.sale_price)';

    /** Per-product price/stock aggregate over its variants, joined as `pv`. */
    private static function variantAggregate(): string
    {
        $onSale = '(' . self::SALE_ACTIVE . ' AND ' . self::SALE . ' IS NOT NULL AND ' . self::SALE . ' < ' . self::REGULAR . ')';
        $current = "CASE WHEN {$onSale} THEN " . self::SALE . ' ELSE ' . self::REGULAR . ' END';
        return "JOIN (
            SELECT v.product_id,
                   MIN({$current}) AS price_min,
                   MAX({$current}) AS price_max,
                   MIN(" . self::REGULAR . ") AS regular_min,
                   MAX({$onSale}) AS on_sale,
                   SUM(v.stock) AS stock_total,
                   COUNT(*) AS variant_count,
                   MIN(v.id) AS first_variant_id
            FROM product_variants v JOIN products p2 ON p2.id = v.product_id
            GROUP BY v.product_id
        ) pv ON pv.product_id = p.id";
    }

    private const CARD_COLUMNS = "p.id, p.name, p.slug, p.short_description, p.is_featured, p.created_at, p.category_id,
        c.name AS category_name, c.slug AS category_slug, b.name AS brand_name,
        pv.price_min, pv.price_max, pv.regular_min, pv.on_sale, pv.stock_total, pv.variant_count, pv.first_variant_id,
        (SELECT thumb_path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order, i.id LIMIT 1) AS image,
        (SELECT thumb_path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order, i.id LIMIT 1 OFFSET 1) AS image_alt,
        (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') AS rating_avg,
        (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.status = 'approved') AS rating_count";

    private static function cardFrom(): string
    {
        return 'FROM products p
            JOIN categories c ON c.id = p.category_id
            LEFT JOIN brands b ON b.id = p.brand_id
            ' . self::variantAggregate() . "
            WHERE p.status = 'published' AND p.deleted_at IS NULL";
    }

    public const SORTS = [
        'featured'   => 'Featured',
        'newest'     => 'Newest',
        'price_asc'  => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'name'       => 'Name A–Z',
    ];

    /**
     * Storefront listing. Filters: category_ids[], q, min, max, in_stock, sort.
     */
    public function listing(array $f, int $page, int $perPage = 12): array
    {
        $where = '';
        $params = [];
        $relevance = '';

        if (!empty($f['category_ids'])) {
            $where .= ' AND p.category_id IN (' . implode(',', array_fill(0, count($f['category_ids']), '?')) . ')';
            array_push($params, ...$f['category_ids']);
        }
        if (!empty($f['q'])) {
            [$clause, $bind] = $this->searchClause($f['q']);
            $where .= " AND {$clause}";
            array_push($params, ...$bind);
            $relevance = $this->fulltextTerms($f['q']);
        }
        if (isset($f['min']) && $f['min'] !== null) {
            $where .= ' AND pv.price_min >= ?';
            $params[] = $f['min'];
        }
        if (isset($f['max']) && $f['max'] !== null) {
            $where .= ' AND pv.price_min <= ?';
            $params[] = $f['max'];
        }
        if (!empty($f['in_stock'])) {
            $where .= ' AND pv.stock_total > 0';
        }
        if (!empty($f['on_sale'])) {
            $where .= ' AND pv.on_sale = 1';
        }

        $sort = $f['sort'] ?? 'featured';
        $order = match ($sort) {
            'newest'     => 'p.created_at DESC',
            'price_asc'  => 'pv.price_min ASC',
            'price_desc' => 'pv.price_min DESC',
            'name'       => 'p.name ASC',
            default      => $relevance !== '' ? 'relevance DESC' : 'p.is_featured DESC, (pv.stock_total > 0) DESC, p.created_at DESC',
        } . ', p.id DESC';

        $select = self::CARD_COLUMNS;
        if ($relevance !== '') {
            $select .= ', MATCH(p.name, p.short_description, p.description) AGAINST (? IN BOOLEAN MODE) AS relevance';
            array_unshift($params, $relevance);
        }
        // The relevance placeholder sits in SELECT, so it must be bound first; the count query skips SELECT columns.
        return $this->paginateWithLeadingSelectParam($select, self::cardFrom() . $where, $params, $order, $page, $perPage, $relevance !== '');
    }

    private function paginateWithLeadingSelectParam(string $select, string $from, array $params, string $order, int $page, int $perPage, bool $leading): array
    {
        $countParams = $leading ? array_slice($params, 1) : $params;
        $total = (int) $this->value("SELECT COUNT(*) {$from}", $countParams);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $items = $this->all(
            "SELECT {$select} {$from} ORDER BY {$order} LIMIT " . (int) $perPage . ' OFFSET ' . (int) (($page - 1) * $perPage),
            $params
        );
        return ['items' => $items, 'total' => $total, 'page' => $page, 'pages' => $pages, 'per_page' => $perPage];
    }

    /** Builds a boolean-mode query from free text: each word must appear, the last is prefix-matched. */
    private function fulltextTerms(string $q): string
    {
        $words = preg_split('/\s+/u', trim((string) preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $q))) ?: [];
        $words = array_values(array_filter($words, static fn ($w) => mb_strlen($w) >= 3));
        if (!$words) {
            return '';
        }
        $last = array_pop($words);
        return implode(' ', array_map(static fn ($w) => '+' . $w, $words)) . ' +' . $last . '*';
    }

    /** FULLTEXT ignores words shorter than 3 characters, so very short queries fall back to LIKE. */
    private function searchClause(string $q): array
    {
        $terms = $this->fulltextTerms($q);
        if ($terms === '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            return ['(p.name LIKE ? OR p.short_description LIKE ?)', [$like, $like]];
        }
        return ['MATCH(p.name, p.short_description, p.description) AGAINST (? IN BOOLEAN MODE)', [$terms]];
    }

    public function suggest(string $q, int $limit = 6): array
    {
        [$clause, $bind] = $this->searchClause($q);
        return $this->all(
            'SELECT p.id, p.name, p.slug, c.name AS category_name, pv.price_min, pv.regular_min, pv.on_sale,
                (SELECT thumb_path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order LIMIT 1) AS image '
            . self::cardFrom() . " AND {$clause} ORDER BY p.is_featured DESC, p.name LIMIT " . (int) $limit,
            $bind
        );
    }

    public function cards(string $condition, string $order, int $limit, array $params = []): array
    {
        return $this->all('SELECT ' . self::CARD_COLUMNS . ' ' . self::cardFrom() . " {$condition} ORDER BY {$order} LIMIT " . (int) $limit, $params);
    }

    public function featured(int $limit = 8): array
    {
        return $this->cards('AND p.is_featured = 1', '(pv.stock_total > 0) DESC, p.created_at DESC', $limit);
    }

    public function newest(int $limit = 8): array
    {
        return $this->cards('', 'p.created_at DESC', $limit);
    }

    public function onSale(int $limit = 4): array
    {
        return $this->cards('AND pv.on_sale = 1', 'p.created_at DESC', $limit);
    }

    public function related(array $product, int $limit = 4): array
    {
        return $this->cards(
            'AND p.id <> ? AND p.category_id IN (SELECT id FROM categories WHERE id = ? OR parent_id = ? OR id = (SELECT parent_id FROM categories WHERE id = ?))',
            '(pv.stock_total > 0) DESC, p.is_featured DESC, p.created_at DESC',
            $limit,
            [$product['id'], $product['category_id'], $product['category_id'], $product['category_id']]
        );
    }

    public function cardsByIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        return $this->cards("AND p.id IN ({$in})", 'p.name', 100, array_values($ids));
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->one(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.parent_id AS category_parent_id,
                b.name AS brand_name, b.origin AS brand_origin
             FROM products p JOIN categories c ON c.id = p.category_id LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.slug = ? AND p.status = 'published' AND p.deleted_at IS NULL",
            [$slug]
        );
    }

    public function findPublished(int $id): ?array
    {
        return $this->one("SELECT * FROM products WHERE id = ? AND status = 'published' AND deleted_at IS NULL", [$id]);
    }

    public function find(int $id): ?array
    {
        return $this->one('SELECT * FROM products WHERE id = ? AND deleted_at IS NULL', [$id]);
    }

    public function images(int $productId): array
    {
        return $this->all('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id', [$productId]);
    }

    public function variants(int $productId): array
    {
        return $this->all('SELECT * FROM product_variants WHERE product_id = ? ORDER BY sort_order, id', [$productId]);
    }

    public function priceCeiling(): float
    {
        return (float) $this->value("SELECT MAX(COALESCE(v.price, p.price)) FROM product_variants v JOIN products p ON p.id = v.product_id
            WHERE p.status = 'published' AND p.deleted_at IS NULL");
    }

    // ---- Admin ----

    public function adminList(array $f, int $page): array
    {
        $where = ['p.deleted_at IS NULL'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(p.name LIKE ? OR EXISTS (SELECT 1 FROM product_variants sv WHERE sv.product_id = p.id AND sv.sku LIKE ?))';
            $like = '%' . addcslashes($f['q'], '%_\\') . '%';
            array_push($params, $like, $like);
        }
        if (in_array($f['status'] ?? '', ['draft', 'published'], true)) {
            $where[] = 'p.status = ?';
            $params[] = $f['status'];
        }
        if (!empty($f['category'])) {
            $where[] = '(p.category_id = ? OR c.parent_id = ?)';
            array_push($params, (int) $f['category'], (int) $f['category']);
        }
        if (($f['stock'] ?? '') === 'low') {
            $where[] = 'pv.stock_total BETWEEN 1 AND ?';
            $params[] = (int) setting('low_stock_threshold', 5);
        } elseif (($f['stock'] ?? '') === 'out') {
            $where[] = 'pv.stock_total = 0';
        }
        $sorts = ['name' => 'p.name', 'price' => 'pv.price_min', 'stock' => 'pv.stock_total', 'updated' => 'p.updated_at', 'status' => 'p.status'];
        $order = ($sorts[$f['sort'] ?? ''] ?? 'p.updated_at') . ' ' . (($f['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC') . ', p.id DESC';

        $select = 'p.id, p.name, p.slug, p.status, p.is_featured, p.updated_at, c.name AS category_name,
            pv.price_min, pv.price_max, pv.on_sale, pv.stock_total, pv.variant_count,
            (SELECT thumb_path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order LIMIT 1) AS image,
            (SELECT GROUP_CONCAT(sku ORDER BY sort_order SEPARATOR \', \') FROM product_variants sv WHERE sv.product_id = p.id) AS skus';
        $from = 'FROM products p JOIN categories c ON c.id = p.category_id LEFT ' . self::variantAggregate() . ' WHERE ' . implode(' AND ', $where);
        return $this->paginate($select, $from, $params, $order, $page, 15);
    }

    public function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) $this->value('SELECT 1 FROM products WHERE slug = ? AND id <> ?', [$slug, $exceptId]);
    }

    public function create(array $data): int
    {
        return $this->insert('products', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->updateRow('products', $id, $data);
    }

    /** Soft delete; the slug is freed so a new product can reuse it. */
    public function softDelete(int $id): void
    {
        $this->run("UPDATE products SET deleted_at = NOW(), status = 'draft', slug = CONCAT(slug, '--deleted-', id) WHERE id = ? AND deleted_at IS NULL", [$id]);
    }

    public function setStatus(array $ids, string $status): void
    {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $this->run("UPDATE products SET status = ? WHERE id IN ({$in}) AND deleted_at IS NULL", [$status, ...$ids]);
    }

    public function namesByIds(array $ids): array
    {
        $in = implode(',', array_fill(0, count($ids), '?'));
        return $this->run("SELECT id, name FROM products WHERE id IN ({$in})", $ids)->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function lowStockVariants(int $threshold, int $limit = 8): array
    {
        return $this->all(
            "SELECT v.id, v.sku, v.size, v.color, v.stock, p.id AS product_id, p.name
             FROM product_variants v JOIN products p ON p.id = v.product_id
             WHERE p.deleted_at IS NULL AND p.status = 'published' AND v.stock <= ?
             ORDER BY v.stock, p.name LIMIT " . (int) $limit,
            [$threshold]
        );
    }

    public function countLowStock(int $threshold): int
    {
        return (int) $this->value(
            "SELECT COUNT(*) FROM product_variants v JOIN products p ON p.id = v.product_id
             WHERE p.deleted_at IS NULL AND p.status = 'published' AND v.stock <= ?",
            [$threshold]
        );
    }
}
