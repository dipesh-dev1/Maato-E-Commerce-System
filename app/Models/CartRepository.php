<?php
declare(strict_types=1);

namespace App\Models;

/** Database cart for signed-in customers. Guests use the session (see CartService). */
final class CartRepository extends Repository
{
    private function cartId(int $userId): int
    {
        $id = $this->value('SELECT id FROM carts WHERE user_id = ?', [$userId]);
        if ($id !== null) {
            return (int) $id;
        }
        $this->run('INSERT IGNORE INTO carts (user_id) VALUES (?)', [$userId]);
        return (int) $this->value('SELECT id FROM carts WHERE user_id = ?', [$userId]);
    }

    /** @return array<int,int> variant_id => quantity */
    public function items(int $userId): array
    {
        $rows = $this->all(
            'SELECT ci.variant_id, ci.quantity FROM cart_items ci JOIN carts c ON c.id = ci.cart_id WHERE c.user_id = ? ORDER BY ci.id',
            [$userId]
        );
        return array_map('intval', array_column($rows, 'quantity', 'variant_id'));
    }

    public function set(int $userId, int $variantId, int $qty): void
    {
        $this->run(
            'INSERT INTO cart_items (cart_id, variant_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)',
            [$this->cartId($userId), $variantId, $qty]
        );
    }

    public function remove(int $userId, int $variantId): void
    {
        $this->run('DELETE ci FROM cart_items ci JOIN carts c ON c.id = ci.cart_id WHERE c.user_id = ? AND ci.variant_id = ?', [$userId, $variantId]);
    }

    public function clear(int $userId): void
    {
        $this->run('DELETE ci FROM cart_items ci JOIN carts c ON c.id = ci.cart_id WHERE c.user_id = ?', [$userId]);
    }
}
