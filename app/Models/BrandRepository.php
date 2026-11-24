<?php
declare(strict_types=1);

namespace App\Models;

final class BrandRepository extends Repository
{
    public function withCounts(): array
    {
        return $this->all('SELECT b.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id AND p.deleted_at IS NULL) AS product_count
            FROM brands b ORDER BY b.name');
    }

    public function active(): array
    {
        return $this->all('SELECT id, name, slug FROM brands WHERE is_active = 1 ORDER BY name');
    }

    public function find(int $id): ?array
    {
        return $this->one('SELECT * FROM brands WHERE id = ?', [$id]);
    }

    public function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) $this->value('SELECT 1 FROM brands WHERE slug = ? AND id <> ?', [$slug, $exceptId]);
    }

    public function create(array $data): int
    {
        return $this->insert('brands', $data);
    }

    public function update(int $id, array $data): void
    {
        $this->updateRow('brands', $id, $data);
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM brands WHERE id = ?', [$id]);
    }
}
