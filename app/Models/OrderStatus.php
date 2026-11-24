<?php
declare(strict_types=1);

namespace App\Models;

final class OrderStatus
{
    public const FLOW = ['pending', 'confirmed', 'packed', 'shipped', 'delivered'];

    public const LABELS = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'packed'    => 'Packed',
        'shipped'   => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    /** Allowed next states. Once shipped, an order can only be delivered. */
    public const TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['packed', 'cancelled'],
        'packed'    => ['shipped', 'cancelled'],
        'shipped'   => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public const TONES = [
        'pending' => 'warning', 'confirmed' => 'info', 'packed' => 'info',
        'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'neutral',
    ];

    public const PAYMENT_LABELS = [
        'cash_on_delivery' => 'Cash on delivery',
        'pay_on_pickup'    => 'Pay on pickup',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? ucfirst($status);
    }

    public static function canMove(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }
}
