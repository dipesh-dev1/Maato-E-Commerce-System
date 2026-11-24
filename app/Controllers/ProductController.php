<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\CategoryRepository;
use App\Models\ProductRepository;
use App\Models\ReviewRepository;
use App\Services\Pricing;

final class ProductController extends Controller
{
    public function show(Request $request): Response
    {
        $products = new ProductRepository();
        $product = $this->notFoundUnless($products->findPublishedBySlug((string) $request->param('slug')));
        $id = (int) $product['id'];
        $reviews = new ReviewRepository();
        $userId = Auth::id();

        $parent = $product['category_parent_id'] ? (new CategoryRepository())->find((int) $product['category_parent_id']) : null;

        return $this->view('store/product', [
            'title'        => $product['meta_title'] ?: $product['name'],
            'metaDescription' => $product['meta_description'] ?: $product['short_description'],
            'product'      => $product,
            'parent'       => $parent,
            'images'       => $products->images($id),
            'options'      => self::buildOptions($product, $products->variants($id)),
            'reviews'      => $reviews->approvedForProduct($id),
            'summary'      => $reviews->summary($id),
            'canReview'    => $userId !== null && $reviews->userReceivedProduct($userId, $id) && $reviews->findByUser($id, $userId) === null,
            'ownReview'    => $userId !== null ? $reviews->findByUser($id, $userId) : null,
            'related'      => $products->related($product),
            'wishlist'     => WishlistController::idsForCurrentUser(),
        ]);
    }

    public function quickView(Request $request): Response
    {
        $products = new ProductRepository();
        $product = $this->notFoundUnless($products->findPublished((int) $request->param('id')));
        $id = (int) $product['id'];
        $html = View::capture('store/partials/quick-view', [
            'product' => $product,
            'images'  => $products->images($id),
            'options' => self::buildOptions($product, $products->variants($id)),
            'wishlist'=> WishlistController::idsForCurrentUser(),
        ]);
        return $this->json(['html' => $html]);
    }

    /**
     * Shapes variants for the picker: distinct sizes and colours, plus a compact map the browser uses
     * to resolve a size+colour choice to a variant, its price and its stock.
     */
    public static function buildOptions(array $product, array $variants): array
    {
        $sizes = [];
        $colors = [];
        $map = [];
        foreach ($variants as $v) {
            $price = Pricing::forVariant($product, $v);
            if ($v['size'] !== null) {
                $sizes[$v['size']] = true;
            }
            if ($v['color'] !== null) {
                $colors[$v['color']] ??= $v['color_hex'];
            }
            $map[] = [
                'id'      => (int) $v['id'],
                'size'    => $v['size'],
                'color'   => $v['color'],
                'sku'     => $v['sku'],
                'stock'   => (int) $v['stock'],
                'price'   => money($price['current']),
                'was'     => $price['on_sale'] ? money($price['regular']) : null,
                'off'     => $price['on_sale'] ? Pricing::percentOff($price['regular'], $price['current']) : 0,
            ];
        }
        $inStock = array_values(array_filter($map, static fn ($v) => $v['stock'] > 0));
        return [
            'sizes'    => array_keys($sizes),
            'colors'   => $colors,
            'variants' => $map,
            'default'  => $inStock[0] ?? $map[0] ?? null,
            'in_stock' => (bool) $inStock,
        ];
    }
}
