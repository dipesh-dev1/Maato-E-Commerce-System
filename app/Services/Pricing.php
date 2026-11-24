<?php
declare(strict_types=1);

namespace App\Services;

/** Mirrors the SQL pricing rules in ProductRepository so PHP and listing queries always agree. */
final class Pricing
{
    public static function saleWindowOpen(?string $startsAt, ?string $endsAt): bool
    {
        $now = time();
        return ($startsAt === null || strtotime($startsAt) <= $now) && ($endsAt === null || strtotime($endsAt) > $now);
    }

    /**
     * @param array $row variant columns (price, sale_price) plus product_price, product_sale_price, sale_starts_at, sale_ends_at
     * @return array{regular: float, current: float, on_sale: bool}
     */
    public static function forRow(array $row): array
    {
        $regular = (float) ($row['price'] ?? $row['product_price']);
        $sale = $row['sale_price'] ?? $row['product_sale_price'] ?? null;
        $onSale = $sale !== null && (float) $sale < $regular && self::saleWindowOpen($row['sale_starts_at'] ?? null, $row['sale_ends_at'] ?? null);
        return ['regular' => $regular, 'current' => $onSale ? (float) $sale : $regular, 'on_sale' => $onSale];
    }

    public static function forVariant(array $product, array $variant): array
    {
        return self::forRow([
            'price' => $variant['price'], 'sale_price' => $variant['sale_price'],
            'product_price' => $product['price'], 'product_sale_price' => $product['sale_price'],
            'sale_starts_at' => $product['sale_starts_at'], 'sale_ends_at' => $product['sale_ends_at'],
        ]);
    }

    public static function variantLabel(array $variant): ?string
    {
        $label = implode(' / ', array_filter([$variant['size'] ?? null, $variant['color'] ?? null]));
        return $label === '' ? null : $label;
    }

    public static function percentOff(float $regular, float $current): int
    {
        return $regular > 0 ? (int) round((1 - $current / $regular) * 100) : 0;
    }
}
