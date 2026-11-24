<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\ProductRepository;
use App\Models\WishlistRepository;

final class WishlistController extends Controller
{
    public static function idsForCurrentUser(): array
    {
        $userId = Auth::id();
        return $userId ? (new WishlistRepository())->productIds($userId) : [];
    }

    public function index(Request $request): Response
    {
        $ids = (new WishlistRepository())->productIds((int) Auth::id());
        return $this->view('account/wishlist', [
            'title'    => 'Saved for later',
            'products' => (new ProductRepository())->cardsByIds($ids),
            'wishlist' => $ids,
        ]);
    }

    public function toggle(Request $request): Response
    {
        $productId = (int) $request->param('product');
        $product = $this->notFoundUnless((new ProductRepository())->findPublished($productId));
        $saved = (new WishlistRepository())->toggle((int) Auth::id(), $productId);
        $message = $saved ? "Saved {$product['name']} for later." : "Removed {$product['name']} from your saved pieces.";
        if ($request->wantsJson()) {
            return Response::json(['saved' => $saved, 'message' => $message]);
        }
        flash('success', $message);
        return $this->back('/account/wishlist');
    }
}
