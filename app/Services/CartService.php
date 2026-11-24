<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Session;
use App\Models\CartRepository;
use App\Models\CouponRepository;
use App\Models\VariantRepository;

/**
 * One cart API over two stores: the database for signed-in customers, the session for guests.
 * Prices are never stored in the cart; they are recalculated from the catalogue on every read.
 */
final class CartService
{
    public const MAX_PER_LINE = 10;

    private CartRepository $repo;
    private VariantRepository $variants;

    public function __construct()
    {
        $this->repo = new CartRepository();
        $this->variants = new VariantRepository();
    }

    /** @return array<int,int> variant_id => quantity */
    public function raw(): array
    {
        $userId = Auth::id();
        return $userId ? $this->repo->items($userId) : $this->guestItems();
    }

    public function guestItems(): array
    {
        return array_map('intval', (array) Session::get('cart', []));
    }

    private function store(int $variantId, int $qty): void
    {
        $userId = Auth::id();
        if ($userId) {
            $qty > 0 ? $this->repo->set($userId, $variantId, $qty) : $this->repo->remove($userId, $variantId);
            return;
        }
        $cart = $this->guestItems();
        if ($qty > 0) {
            $cart[$variantId] = $qty;
        } else {
            unset($cart[$variantId]);
        }
        Session::set('cart', $cart);
    }

    public function add(int $variantId, int $qty): array
    {
        $variant = $this->variants->find($variantId);
        if ($variant === null) {
            throw new CartException("Sorry, that item isn't available any more.");
        }
        $wanted = ($this->raw()[$variantId] ?? 0) + max(1, $qty);
        return [$variant, $this->setQuantity($variant, $wanted)];
    }

    public function update(int $variantId, int $qty): void
    {
        if ($qty <= 0) {
            $this->store($variantId, 0);
            return;
        }
        $variant = $this->variants->find($variantId);
        if ($variant === null) {
            $this->store($variantId, 0);
            throw new CartException("That item isn't available any more, so we've taken it out of your bag.");
        }
        $this->setQuantity($variant, $qty);
    }

    /** Stores min(wanted, stock, line cap) and explains when the number had to be lowered. */
    private function setQuantity(array $variant, int $wanted): int
    {
        $stock = (int) $variant['stock'];
        if ($stock <= 0) {
            throw new CartException($variant['product_name'] . ' is sold out right now.');
        }
        $qty = min($wanted, $stock, self::MAX_PER_LINE);
        $this->store((int) $variant['id'], $qty);
        if ($qty < $wanted) {
            throw new CartException($qty === $stock
                ? "We only have {$stock} of these, so that's how many are in your bag."
                : 'You can order up to ' . self::MAX_PER_LINE . ' of one item at a time.');
        }
        return $qty;
    }

    public function remove(int $variantId): void
    {
        $this->store($variantId, 0);
    }

    public function clear(): void
    {
        $userId = Auth::id();
        $userId ? $this->repo->clear($userId) : Session::forget('cart');
        Session::forget('coupon_code');
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /** Guest lines are added to whatever the account already had, capped by stock. */
    public function mergeGuestItems(int $userId, array $guestItems): void
    {
        if (!$guestItems) {
            return;
        }
        $existing = $this->repo->items($userId);
        $variants = $this->variants->forCart(array_keys($guestItems));
        foreach ($guestItems as $variantId => $qty) {
            if (!isset($variants[$variantId])) {
                continue;
            }
            $total = min(($existing[$variantId] ?? 0) + $qty, (int) $variants[$variantId]['stock'], self::MAX_PER_LINE);
            if ($total > 0) {
                $this->repo->set($userId, (int) $variantId, $total);
            }
        }
        Session::forget('cart');
    }

    /** Priced, display-ready lines. Unavailable variants are removed from the cart as a side effect. */
    public function lines(): array
    {
        $raw = $this->raw();
        $variants = $this->variants->forCart(array_keys($raw));
        $lines = [];
        foreach ($raw as $variantId => $qty) {
            $v = $variants[$variantId] ?? null;
            if ($v === null) {
                $this->store((int) $variantId, 0);
                continue;
            }
            $price = Pricing::forRow($v);
            $lines[] = [
                'variant_id' => (int) $v['id'],
                'product_id' => (int) $v['product_id'],
                'name'       => $v['product_name'],
                'slug'       => $v['product_slug'],
                'label'      => Pricing::variantLabel($v),
                'sku'        => $v['sku'],
                'image'      => $v['image'],
                'quantity'   => $qty,
                'stock'      => (int) $v['stock'],
                'unit_price' => $price['current'],
                'regular'    => $price['regular'],
                'on_sale'    => $price['on_sale'],
                'line_total' => round($price['current'] * $qty, 2),
                'problem'    => $this->stockProblem((int) $v['stock'], $qty),
            ];
        }
        return $lines;
    }

    private function stockProblem(int $stock, int $qty): ?string
    {
        if ($stock <= 0) {
            return 'Sold out. Remove it to check out.';
        }
        return $qty > $stock ? "Only {$stock} left. Lower the quantity to check out." : null;
    }

    /**
     * Totals for the cart page and checkout. $district decides the delivery fee; null means
     * "not chosen yet" and shipping is shown as calculated at checkout.
     */
    public function summary(array $lines, ?string $district = null, string $method = 'cash_on_delivery'): array
    {
        $subtotal = round(array_sum(array_column($lines, 'line_total')), 2);
        [$coupon, $couponError] = $this->activeCoupon($subtotal);
        $discount = $coupon ? self::discountFor($coupon, $subtotal) : 0.0;
        $shipping = self::shippingFor($subtotal - $discount, $district, $method);
        $taxRate = Settings::taxRate();
        $tax = round(($subtotal - $discount) * $taxRate / 100, 2);

        return [
            'count'        => array_sum(array_column($lines, 'quantity')),
            'subtotal'     => $subtotal,
            'coupon'       => $coupon,
            'coupon_error' => $couponError,
            'discount'     => $discount,
            'shipping'     => $shipping,
            'tax_rate'     => $taxRate,
            'tax'          => $tax,
            'total'        => round($subtotal - $discount + ($shipping ?? 0) + $tax, 2),
            'free_shipping_gap' => max(0, (float) Settings::get('free_shipping_threshold', 0) - ($subtotal - $discount)),
            'has_problems' => (bool) array_filter(array_column($lines, 'problem')),
        ];
    }

    public static function shippingFor(float $amount, ?string $district, string $method): ?float
    {
        if ($method === 'pay_on_pickup') {
            return 0.0;
        }
        $threshold = (float) Settings::get('free_shipping_threshold', 0);
        if ($threshold > 0 && $amount >= $threshold) {
            return 0.0;
        }
        if ($district === null) {
            return null;
        }
        $inValley = in_array(mb_strtolower($district), array_map('mb_strtolower', Settings::valleyDistricts()), true);
        return (float) Settings::get($inValley ? 'shipping_fee_valley' : 'shipping_fee_outside', 0);
    }

    public static function discountFor(array $coupon, float $subtotal): float
    {
        $discount = $coupon['type'] === 'percent'
            ? $subtotal * (float) $coupon['value'] / 100
            : (float) $coupon['value'];
        return round(min($discount, $subtotal), 2);
    }

    /** @return string|null why the coupon can't be used, or null if it can */
    public static function couponProblem(?array $coupon, float $subtotal): ?string
    {
        if ($coupon === null || !$coupon['is_active'] || ($coupon['starts_at'] && strtotime($coupon['starts_at']) > time())) {
            return "That code isn't valid.";
        }
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) <= time()) {
            return 'That code has expired.';
        }
        if ($coupon['usage_limit'] !== null && (int) $coupon['used_count'] >= (int) $coupon['usage_limit']) {
            return 'That code has been fully redeemed.';
        }
        if ($subtotal < (float) $coupon['min_cart_value']) {
            return 'Add ' . money((float) $coupon['min_cart_value'] - $subtotal) . ' more to use ' . $coupon['code'] . '.';
        }
        return null;
    }

    /** @return array{0: ?array, 1: ?string} the applicable coupon, or the reason the stored code no longer applies */
    private function activeCoupon(float $subtotal): array
    {
        $code = Session::get('coupon_code');
        if ($code === null) {
            return [null, null];
        }
        $coupon = (new CouponRepository())->findByCode($code);
        $problem = self::couponProblem($coupon, $subtotal);
        return $problem === null ? [$coupon, null] : [null, $problem];
    }

    public function applyCoupon(string $code): array
    {
        $coupon = (new CouponRepository())->findByCode($code);
        $subtotal = array_sum(array_column($this->lines(), 'line_total'));
        $problem = self::couponProblem($coupon, $subtotal);
        if ($problem !== null) {
            throw new CartException($problem);
        }
        Session::set('coupon_code', $coupon['code']);
        return $coupon;
    }

    public function removeCoupon(): void
    {
        Session::forget('coupon_code');
    }
}
