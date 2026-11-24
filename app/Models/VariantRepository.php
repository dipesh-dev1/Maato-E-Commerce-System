<?php
declare(strict_types=1);

namespace App\Models;

final class VariantRepository extends Repository
{
    private const CART_SELECT = "SELECT v.id, v.product_id, v.sku, v.size, v.color, v.price, v.sale_price, v.stock,
            p.name AS product_name, p.slug AS product_slug, p.price AS product_price, p.sale_price AS product_sale_price,
            p.sale_starts_at, p.sale_ends_at,
            (SELECT thumb_path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order, i.id LIMIT 1) AS image
        FROM product_variants v JOIN products p ON p.id = v.product_id
        WHERE p.status = 'published' AND p.deleted_at IS NULL";

    /** Purchasable variants keyed by id. Variants of drafted or deleted products silently drop out. */
    public function forCart(array $ids): array
    {
        return $this->keyed($ids, '');
    }

    /** Same rows, with an exclusive row lock held until the surrounding transaction ends. */
    public function lockForCheckout(array $ids): array
    {
        return $this->keyed($ids, ' FOR UPDATE');
    }

    private function keyed(array $ids, string $suffix): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->all(self::CART_SELECT . " AND v.id IN ({$in}) ORDER BY v.id" . $suffix, $ids);
        return array_column($rows, null, 'id');
    }

    public function find(int $id): ?array
    {
        return $this->one(self::CART_SELECT . ' AND v.id = ?', [$id]);
    }

    public function decrementStock(int $id, int $qty): void
    {
        $this->run('UPDATE product_variants SET stock = stock - ? WHERE id = ? AND stock >= ?', [$qty, $id, $qty]);
    }

    public function incrementStock(int $id, int $qty): void
    {
        $this->run('UPDATE product_variants SET stock = stock + ? WHERE id = ?', [$qty, $id]);
    }

    public function skuTaken(string $sku, int $exceptId = 0): bool
    {
        return (bool) $this->value('SELECT 1 FROM product_variants WHERE sku = ? AND id <> ?', [$sku, $exceptId]);
    }

    /**
     * Replaces a product's variants with $rows. Rows carrying an existing id are updated in place
     * (so carts and order history keep pointing at them); the rest are inserted; missing ones deleted.
     */
    public function sync(int $productId, array $rows): void
    {
        $existing = array_map('intval', $this->run('SELECT id FROM product_variants WHERE product_id = ?', [$productId])->fetchAll(\PDO::FETCH_COLUMN));
        $kept = [];
        foreach (array_values($rows) as $i => $row) {
            $data = [
                'sku' => $row['sku'], 'size' => $row['size'], 'color' => $row['color'], 'color_hex' => $row['color_hex'],
                'price' => $row['price'], 'sale_price' => $row['sale_price'], 'stock' => $row['stock'], 'sort_order' => $i,
            ];
            $id = (int) ($row['id'] ?? 0);
            if ($id && in_array($id, $existing, true)) {
                $this->updateRow('product_variants', $id, $data);
                $kept[] = $id;
            } else {
                $this->insert('product_variants', ['product_id' => $productId] + $data);
            }
        }
        $remove = array_diff($existing, $kept);
        if ($remove) {
            $in = implode(',', array_fill(0, count($remove), '?'));
            $this->run("DELETE FROM product_variants WHERE id IN ({$in})", array_values($remove));
        }
    }
}
