<?php
declare(strict_types=1);

namespace App\Models;

final class ProductImageRepository extends Repository
{
    public function add(int $productId, string $path, string $thumb, string $alt): void
    {
        $next = (int) $this->value('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM product_images WHERE product_id = ?', [$productId]);
        $this->insert('product_images', ['product_id' => $productId, 'path' => $path, 'thumb_path' => $thumb, 'alt' => $alt, 'sort_order' => $next]);
    }

    /** Deletes the given images of this product and returns their file paths for cleanup. */
    public function remove(int $productId, array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->all("SELECT path, thumb_path FROM product_images WHERE product_id = ? AND id IN ({$in})", [$productId, ...$ids]);
        $this->run("DELETE FROM product_images WHERE product_id = ? AND id IN ({$in})", [$productId, ...$ids]);
        return array_merge(array_column($rows, 'path'), array_column($rows, 'thumb_path'));
    }

    /** Applies the order of $ids (images not listed keep their relative order after them). */
    public function reorder(int $productId, array $ids): void
    {
        $stmt = $this->db->prepare('UPDATE product_images SET sort_order = ? WHERE id = ? AND product_id = ?');
        foreach (array_values($ids) as $position => $id) {
            $stmt->execute([$position, $id, $productId]);
        }
    }
}
