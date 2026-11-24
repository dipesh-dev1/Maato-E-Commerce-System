<?php
declare(strict_types=1);

namespace App\Models;

final class CategoryRepository extends Repository
{
    public function allActive(): array
    {
        return $this->all('SELECT * FROM categories WHERE is_active = 1 ORDER BY parent_id IS NOT NULL, sort_order, name');
    }

    /** Top-level categories with their active children attached, for menus. */
    public function tree(bool $activeOnly = true): array
    {
        $rows = $activeOnly
            ? $this->allActive()
            : $this->all('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.deleted_at IS NULL) AS product_count
                FROM categories c ORDER BY c.parent_id IS NOT NULL, c.sort_order, c.name');
        $roots = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === null) {
                $roots[$row['id']] = $row + ['children' => []];
            }
        }
        foreach ($rows as $row) {
            if ($row['parent_id'] !== null && isset($roots[$row['parent_id']])) {
                $roots[$row['parent_id']]['children'][] = $row;
            }
        }
        return array_values($roots);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->one('SELECT * FROM categories WHERE slug = ? AND is_active = 1', [$slug]);
    }

    public function find(int $id): ?array
    {
        return $this->one('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    /** The category plus every descendant id (two levels are supported by the admin UI, recursion handles more). */
    public function descendantIds(int $id): array
    {
        $ids = [$id];
        $frontier = [$id];
        while ($frontier) {
            $in = implode(',', array_fill(0, count($frontier), '?'));
            $frontier = array_map('intval', $this->run("SELECT id FROM categories WHERE parent_id IN ({$in})", $frontier)->fetchAll(\PDO::FETCH_COLUMN));
            $ids = array_merge($ids, $frontier);
        }
        return $ids;
    }

    public function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) $this->value('SELECT 1 FROM categories WHERE slug = ? AND id <> ?', [$slug, $exceptId]);
    }

    public function create(array $data): int
    {
        return $this->insert('categories', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->updateRow('categories', $id, $data);
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM categories WHERE id = ?', [$id]);
    }

    public function hasChildren(int $id): bool
    {
        return (bool) $this->value('SELECT 1 FROM categories WHERE parent_id = ? LIMIT 1', [$id]);
    }

    public function isInUse(int $id): bool
    {
        return (bool) $this->value(
            'SELECT 1 FROM products WHERE category_id = ? UNION SELECT 1 FROM categories WHERE parent_id = ? LIMIT 1',
            [$id, $id]
        );
    }
}
