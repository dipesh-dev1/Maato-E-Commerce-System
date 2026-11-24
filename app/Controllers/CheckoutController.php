<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\AddressRepository;
use App\Services\CartException;
use App\Services\CartService;
use App\Services\CheckoutService;

final class CheckoutController extends Controller
{
    public function show(Request $request): Response
    {
        $cart = new CartService();
        $lines = $cart->lines();
        if (!$lines) {
            flash('info', 'Your bag is empty. Add something first.');
            return $this->redirect(route('cart'));
        }
        $addresses = (new AddressRepository())->forUser((int) Auth::id());
        $selectedId = (int) old('address_id', $request->query('address', $addresses[0]['id'] ?? 0));
        $byId = array_column($addresses, null, 'id');
        $default = $byId[$selectedId] ?? $addresses[0] ?? null;
        $method = old('payment_method', 'cash_on_delivery');

        return $this->view('store/checkout', [
            'title'     => 'Checkout',
            'lines'     => $lines,
            'addresses' => $addresses,
            'selectedAddress' => (int) ($default['id'] ?? 0),
            'method'    => $method,
            'summary'   => $cart->summary($lines, $default['district'] ?? null, $method),
            'pickupEnabled' => setting('pickup_enabled') === '1',
            'quotes'    => $this->quotes($cart, $lines, $addresses),
        ], 200, 'layouts/checkout');
    }

    /** Pre-computed delivery fee and total for every address (and pickup), so switching updates instantly. */
    private function quotes(CartService $cart, array $lines, array $addresses): array
    {
        $format = static fn (array $s): array => [
            'shipping' => $s['shipping'] > 0 ? money($s['shipping']) : 'Free',
            'total'    => money($s['total']),
        ];
        $quotes = ['pickup' => $format($cart->summary($lines, null, 'pay_on_pickup'))];
        foreach ($addresses as $a) {
            $quotes[(string) $a['id']] = $format($cart->summary($lines, $a['district']));
        }
        return $quotes;
    }

    public function place(Request $request): Response
    {
        $methods = setting('pickup_enabled') === '1' ? 'cash_on_delivery,pay_on_pickup' : 'cash_on_delivery';
        $data = $this->validate($request, [
            'payment_method' => 'required|in:' . $methods,
            'address_id'     => 'nullable|integer',
            'contact_phone'  => $request->input('payment_method') === 'pay_on_pickup' ? 'required|phone' : 'nullable',
            'note'           => 'nullable|string|max:500',
        ], ['payment_method' => 'how you would like to pay', 'contact_phone' => 'a phone number']);

        try {
            $number = (new CheckoutService())->place(Auth::user(), $data);
        } catch (CartException $e) {
            flash('error', $e->getMessage());
            return $this->redirect(route('checkout'));
        }
        flash('success', "Thank you! Order {$number} is in. We'll call to confirm within a few hours.");
        return $this->redirect(route('account.order', ['number' => $number]) . '?placed=1');
    }
}
