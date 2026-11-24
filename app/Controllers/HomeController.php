<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\CategoryRepository;
use App\Models\ProductRepository;

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $products = new ProductRepository();
        return $this->view('store/home', [
            'title'      => setting('store_name', 'Maato') . ': ' . setting('store_tagline', ''),
            'metaDescription' => 'Stoneware, lighting, furniture and linen made by small workshops across Nepal. Delivered across the country, cash on delivery.',
            'featured'   => $products->featured(8),
            'newest'     => $products->newest(4),
            'onSale'     => $products->onSale(3),
            'categories' => (new CategoryRepository())->tree(),
            'wishlist'   => WishlistController::idsForCurrentUser(),
        ]);
    }
}
