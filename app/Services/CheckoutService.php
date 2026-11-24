<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;
use App\Models\AddressRepository;
use App\Models\CartRepository;
use App\Models\CouponRepository;
use App\Models\OrderRepository;
use App\Models\VariantRepository;

final class CheckoutService
{
    /**
     * Places the order atomically: variant rows (and the coupon row) are locked, stock is re-checked
     * against the locked values, prices are recalculated, and stock is decremented before commit.
     *
     * @param array $input address_id, payment_method, note, contact_phone
     * @return string the new order number
     * @throws CartException with a shopper-facing explanation
     */
    public function place(array $user, array $input): string
    {
        $userId = (int) $user['id'];
        $method = $input['payment_method'];

        $address = null;
        if ($method === 'cash_on_delivery') {
            $address = (new AddressRepository())->findForUser((int) ($input['address_id'] ?? 0), $userId);
            if ($address === null) {
                throw new CartException('Choose a delivery address, or add one.');
            }
        }

        return Database::transaction(function () use ($user, $userId, $method, $address, $input): string {
            $raw = (new CartRepository())->items($userId);
            if (!$raw) {
                throw new CartException('Your bag is empty.');
            }

            $variantRepo = new VariantRepository();
            $locked = $variantRepo->lockForCheckout(array_keys($raw));
            [$lines, $problems] = $this->priceLines($raw, $locked);
            if ($problems) {
                throw new CartException(implode(' ', $problems));
            }
            $subtotal = round(array_sum(array_column($lines, 'line_total')), 2);

            [$coupon, $discount] = $this->lockCoupon($subtotal);
            $district = $address['district'] ?? null;
            $shipping = (float) CartService::shippingFor($subtotal - $discount, $district, $method);
            $taxRate = Settings::taxRate();
            $tax = round(($subtotal - $discount) * $taxRate / 100, 2);
            $total = round($subtotal - $discount + $shipping + $tax, 2);

            $orders = new OrderRepository();
            $number = $this->newOrderNumber($orders);
            $orderId = $orders->create([
                'order_number' => $number, 'user_id' => $userId, 'status' => 'pending', 'payment_method' => $method,
                'subtotal' => $subtotal, 'discount_total' => $discount, 'shipping_total' => $shipping,
                'tax_rate' => $taxRate, 'tax_total' => $tax, 'grand_total' => $total,
                'currency' => Settings::get('currency_code', 'NPR'),
                'coupon_id' => $coupon['id'] ?? null, 'coupon_code' => $coupon['code'] ?? null,
                'customer_name' => $user['name'], 'customer_email' => $user['email'], 'customer_phone' => $user['phone'],
                'note' => $input['note'] ?: null,
            ] + $this->addressSnapshot($address, $user, $input));

            foreach ($lines as $line) {
                $orders->addItem($orderId, [
                    'product_id' => $line['product_id'], 'variant_id' => $line['variant_id'],
                    'product_name' => $line['name'], 'variant_label' => $line['label'], 'sku' => $line['sku'],
                    'image_path' => $line['image'], 'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'], 'line_total' => $line['line_total'],
                ]);
                $variantRepo->decrementStock($line['variant_id'], $line['quantity']);
            }
            $orders->addHistory($orderId, 'pending', 'Order placed.', $userId);
            if ($coupon) {
                (new CouponRepository())->incrementUsage((int) $coupon['id']);
            }

            (new CartRepository())->clear($userId);
            Session::forget('coupon_code');
            return $number;
        });
    }

    /** @return array{0: array, 1: string[]} priced lines, and stock problems in plain words */
    private function priceLines(array $raw, array $locked): array
    {
        $lines = [];
        $problems = [];
        foreach ($raw as $variantId => $qty) {
            $v = $locked[$variantId] ?? null;
            if ($v === null) {
                $problems[] = 'One item in your bag is no longer available; please remove it.';
                continue;
            }
            $label = trim($v['product_name'] . ' ' . (Pricing::variantLabel($v) ? '(' . Pricing::variantLabel($v) . ')' : ''));
            $stock = (int) $v['stock'];
            if ($stock <= 0) {
                $problems[] = "{$label} has just sold out.";
                continue;
            }
            if ($stock < $qty) {
                $problems[] = "Only {$stock} of {$label} left; you have {$qty} in your bag.";
                continue;
            }
            $price = Pricing::forRow($v);
            $lines[] = [
                'variant_id' => (int) $v['id'], 'product_id' => (int) $v['product_id'], 'name' => $v['product_name'],
                'label' => Pricing::variantLabel($v), 'sku' => $v['sku'], 'image' => $v['image'],
                'unit_price' => $price['current'], 'quantity' => $qty, 'line_total' => round($price['current'] * $qty, 2),
            ];
        }
        return [$lines, $problems];
    }

    /** Locks the coupon row so two checkouts can't both take its last use. */
    private function lockCoupon(float $subtotal): array
    {
        $code = Session::get('coupon_code');
        if ($code === null) {
            return [null, 0.0];
        }
        $coupon = (new CouponRepository())->findByCode($code, true);
        $problem = CartService::couponProblem($coupon, $subtotal);
        if ($problem !== null) {
            Session::forget('coupon_code');
            throw new CartException("We've removed the code {$code} from your bag: {$problem} Please review your total.");
        }
        return [$coupon, CartService::discountFor($coupon, $subtotal)];
    }

    private function addressSnapshot(?array $address, array $user, array $input): array
    {
        if ($address !== null) {
            return [
                'ship_recipient' => $address['recipient_name'], 'ship_phone' => $address['phone'],
                'ship_line1' => $address['line1'], 'ship_line2' => $address['line2'], 'ship_city' => $address['city'],
                'ship_district' => $address['district'], 'ship_province' => $address['province'],
                'ship_postal_code' => $address['postal_code'],
            ];
        }
        // Pickup orders record the studio as the destination and the customer's contact number.
        return [
            'ship_recipient' => $user['name'], 'ship_phone' => $input['contact_phone'],
            'ship_line1' => 'Store pickup: ' . Settings::get('pickup_address', ''), 'ship_line2' => null,
            'ship_city' => 'Lalitpur', 'ship_district' => 'Lalitpur', 'ship_province' => 'Bagmati', 'ship_postal_code' => null,
        ];
    }

    private function newOrderNumber(OrderRepository $orders): string
    {
        do {
            $number = 'MT' . random_int(100000, 999999);
        } while ($orders->numberExists($number));
        return $number;
    }
}
