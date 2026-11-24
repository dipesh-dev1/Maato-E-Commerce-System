<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\CouponRepository;
use App\Models\OrderRepository;
use App\Models\OrderStatus;
use App\Models\VariantRepository;

final class OrderService
{
    /** Customers may cancel only while the order is still pending. */
    public function cancelByCustomer(int $orderId, int $userId): void
    {
        Database::transaction(function () use ($orderId, $userId): void {
            $orders = new OrderRepository();
            $order = $orders->find($orderId, true);
            if ($order === null || (int) $order['user_id'] !== $userId) {
                throw new CartException("We couldn't find that order.");
            }
            if ($order['status'] !== 'pending') {
                throw new CartException('This order is already being prepared, so it can no longer be cancelled online. Please call us and we will help.');
            }
            $this->applyCancellation($orders, $order);
            $orders->addHistory($orderId, 'cancelled', 'Cancelled by the customer.', $userId);
        });
    }

    /** Back-office status change, validated against the allowed transitions. */
    public function changeStatus(int $orderId, string $to, ?string $note, int $actorId): array
    {
        return Database::transaction(function () use ($orderId, $to, $note, $actorId): array {
            $orders = new OrderRepository();
            $order = $orders->find($orderId, true);
            if ($order === null) {
                throw new CartException('Order not found.');
            }
            if (!OrderStatus::canMove($order['status'], $to)) {
                throw new CartException(sprintf(
                    "An order can't go from %s to %s.",
                    OrderStatus::label($order['status']),
                    OrderStatus::label($to)
                ));
            }
            if ($to === 'cancelled') {
                $this->applyCancellation($orders, $order);
            } else {
                $orders->setStatus($orderId, $to);
            }
            $orders->addHistory($orderId, $to, $note, $actorId);
            return $order;
        });
    }

    /** Returns stock to the shelf and gives back the coupon use. Caller holds the order lock. */
    private function applyCancellation(OrderRepository $orders, array $order): void
    {
        $variants = new VariantRepository();
        foreach ($orders->items((int) $order['id']) as $item) {
            if ($item['variant_id'] !== null) {
                $variants->incrementStock((int) $item['variant_id'], (int) $item['quantity']);
            }
        }
        if ($order['coupon_id'] !== null) {
            (new CouponRepository())->decrementUsage((int) $order['coupon_id']);
        }
        $orders->setStatus((int) $order['id'], 'cancelled');
    }
}
