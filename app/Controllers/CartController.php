<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\AddressRepository;
use App\Models\ProductRepository;
use App\Services\CartException;
use App\Services\CartService;

/** Every mutating action answers JSON for the drawer/cart page, or redirects back when JavaScript is off. */
final class CartController extends Controller
{
    private CartService $cart;

    public function __construct()
    {
        $this->cart = new CartService();
    }

    public function show(Request $request): Response
    {
        return $this->view('store/cart', ['title' => 'Your bag'] + $this->state());
    }

    public function drawer(Request $request): Response
    {
        return $this->json($this->payload());
    }

    public function add(Request $request): Response
    {
        return $this->respond($request, function () use ($request): string {
            $variantId = $this->resolveVariant($request);
            [$variant] = $this->cart->add($variantId, max(1, (int) $request->input('quantity', 1)));
            return 'Added to your bag: ' . $variant['product_name'] . '.';
        });
    }

    public function update(Request $request): Response
    {
        return $this->respond($request, function () use ($request): string {
            $this->cart->update((int) $request->param('variant'), (int) $request->input('quantity', 1));
            return 'Bag updated.';
        });
    }

    public function remove(Request $request): Response
    {
        return $this->respond($request, function () use ($request): string {
            $this->cart->remove((int) $request->param('variant'));
            return 'Removed from your bag.';
        });
    }

    public function applyCoupon(Request $request): Response
    {
        return $this->respond($request, function () use ($request): string {
            $code = mb_substr((string) $request->input('code', ''), 0, 30);
            if ($code === '') {
                throw new CartException('Enter a code first.');
            }
            $coupon = $this->cart->applyCoupon($code);
            return 'Code ' . $coupon['code'] . ' applied.';
        });
    }

    public function removeCoupon(Request $request): Response
    {
        return $this->respond($request, function (): string {
            $this->cart->removeCoupon();
            return 'Code removed.';
        });
    }

    private function respond(Request $request, callable $action): Response
    {
        try {
            $message = $action();
            if ($request->wantsJson()) {
                return Response::json($this->payload($message));
            }
            flash('success', $message);
        } catch (CartException $e) {
            if ($request->wantsJson()) {
                return Response::json($this->payload(), 422, $e->getMessage());
            }
            flash('error', $e->getMessage());
        }
        return $this->back('/cart');
    }

    /** Accepts an explicit variant id, or a product id plus the chosen size/colour (no-JS form). */
    private function resolveVariant(Request $request): int
    {
        $productId = (int) $request->input('product_id', 0);
        $variantId = (int) $request->input('variant_id', 0);
        $variants = $productId ? (new ProductRepository())->variants($productId) : [];
        foreach ($variants as $v) {
            if ($variantId && (int) $v['id'] === $variantId) {
                return $variantId;
            }
            if (!$variantId
                && ($v['size'] ?? '') === (string) $request->input('size', '')
                && ($v['color'] ?? '') === (string) $request->input('color', '')) {
                return (int) $v['id'];
            }
        }
        throw new CartException('Please choose a size and colour that are available.');
    }

    private function state(): array
    {
        $lines = $this->cart->lines();
        $district = null;
        if ($userId = Auth::id()) {
            $default = (new AddressRepository())->forUser($userId)[0] ?? null;
            $district = $default['district'] ?? null;
        }
        return ['lines' => $lines, 'summary' => $this->cart->summary($lines, $district)];
    }

    private function payload(?string $message = null): array
    {
        $state = $this->state();
        return [
            'message' => $message,
            'count'   => $state['summary']['count'],
            'drawer'  => View::capture('store/partials/cart-drawer-body', $state),
            'page'    => View::capture('store/partials/cart-page-body', $state),
        ];
    }
}
