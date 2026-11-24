<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\CategoryRepository;
use App\Models\ProductRepository;

final class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->listing($request, [
            'title' => 'Shop everything',
            'intro' => 'Everything we make and stock, from first-cup mugs to the sofa you will keep for twenty years.',
        ]);
    }

    public function category(Request $request): Response
    {
        $categories = new CategoryRepository();
        $category = $this->notFoundUnless($categories->findBySlug((string) $request->param('slug')));
        $parent = $category['parent_id'] ? $categories->find((int) $category['parent_id']) : null;
        return $this->listing($request, [
            'title'    => $category['name'],
            'intro'    => $category['description'] ?: $category['tagline'],
            'category' => $category,
            'parent'   => $parent,
            'category_ids' => $categories->descendantIds((int) $category['id']),
        ]);
    }

    public function search(Request $request): Response
    {
        $q = $this->query($request);
        return $this->listing($request, [
            'title' => $q === '' ? 'Search' : 'Results for “' . $q . '”',
            'intro' => null,
            'isSearch' => true,
        ]);
    }

    /** Instant suggestions for the header search. */
    public function suggest(Request $request): Response
    {
        $q = $this->query($request);
        if (mb_strlen($q) < 2) {
            return $this->json(['items' => [], 'query' => $q]);
        }
        $items = array_map(static fn (array $p): array => [
            'name'     => $p['name'],
            'category' => $p['category_name'],
            'url'      => route('product', ['slug' => $p['slug']]),
            'image'    => media($p['image']),
            'price'    => money($p['price_min']),
            'was'      => $p['on_sale'] ? money($p['regular_min']) : null,
        ], (new ProductRepository())->suggest($q));
        return $this->json(['items' => $items, 'query' => $q, 'all_url' => url('/search', ['q' => $q])]);
    }

    private function query(Request $request): string
    {
        return mb_substr(trim((string) $request->query('q', '')), 0, 80);
    }

    private function listing(Request $request, array $context): Response
    {
        $filters = $this->filters($request);
        $filters['category_ids'] = $context['category_ids'] ?? [];
        $products = new ProductRepository();
        $isSearch = $context['isSearch'] ?? false;
        $result = ($isSearch && $filters['q'] === '')
            ? ['items' => [], 'total' => 0, 'page' => 1, 'pages' => 1, 'per_page' => 12]
            : $products->listing($filters, max(1, (int) $request->query('page', 1)));

        $data = $context + [
            'filters'  => $filters,
            'result'   => $result,
            'sorts'    => ProductRepository::SORTS,
            'tree'     => (new CategoryRepository())->tree(),
            'wishlist' => WishlistController::idsForCurrentUser(),
            'priceCeiling' => (int) (ceil($products->priceCeiling() / 1000) * 1000),
            'isSearch' => $isSearch,
            'category' => $context['category'] ?? null,
            'parent'   => $context['parent'] ?? null,
        ];

        if ($request->wantsJson()) {
            return $this->json([
                'html'  => View::capture('store/partials/listing-results', $data),
                'total' => $result['total'],
                'title' => $data['title'],
            ]);
        }
        return $this->view('store/listing', $data);
    }

    private function filters(Request $request): array
    {
        $num = static function ($v): ?int {
            return is_numeric($v) && (int) $v >= 0 ? (int) $v : null;
        };
        $sort = (string) $request->query('sort', 'featured');
        return [
            'q'        => $this->query($request),
            'min'      => $num($request->query('min')),
            'max'      => $num($request->query('max')),
            'in_stock' => $request->query('in_stock') === '1',
            'on_sale'  => $request->query('on_sale') === '1',
            'sort'     => array_key_exists($sort, ProductRepository::SORTS) ? $sort : 'featured',
        ];
    }
}
