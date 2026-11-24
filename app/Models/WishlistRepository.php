<?php
declare(strict_types=1);

namespace App\Models;

final class WishlistRepository extends Repository
{
    public function productIds(int $userId): array
    {
        return array_map('intval', $this->run('SELECT product_id FROM wishlists WHERE user_id = ? ORDER BY created_at DESC', [$userId])->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** @return bool true if the product is now saved */
    public function toggle(int $userId, int $productId): bool
    {
        $deleted = $this->run('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?', [$userId, $productId])->rowCount();
        if ($deleted) {
            return false;
        }
        $this->run('INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)', [$userId, $productId]);
        return true;
    }
}
