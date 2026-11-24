<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Core\Validator;
use App\Models\BrandRepository;
use App\Models\CategoryRepository;
use App\Models\ProductImageRepository;
use App\Models\ProductRepository;
use App\Models\VariantRepository;
use App\Services\ActivityLogger;
use App\Services\ImageUploader;
use App\Services\UploadException;

final class ProductController extends AdminController
{
    private const RULES = [
        'name'              => 'required|string|min:2|max:160',
        'slug'              => 'nullable|slug|max:180',
        'category_id'       => 'required|integer',
        'brand_id'          => 'nullable|integer',
        'short_description' => 'required|string|max:255',
        'description'       => 'required|string|max:10000',
        'details'           => 'nullable|string|max:3000',
        'price'             => 'required|numeric|min:0|max:10000000',
        'sale_price'        => 'nullable|numeric|min:0|max:10000000',
        'sale_starts_at'    => 'nullable|date',
        'sale_ends_at'      => 'nullable|date',
        'status'            => 'required|in:draft,published',
        'is_featured'       => 'boolean',
        'size_guide'        => 'nullable|in:bedding,rug,furniture',
        'meta_title'        => 'nullable|string|max:160',
        'meta_description'  => 'nullable|string|max:255',
    ];

    private const VARIANT_RULES = [
        'id'         => 'nullable|integer',
        'sku'        => 'required|string|max:64',
        'size'       => 'nullable|string|max:40',
        'color'      => 'nullable|string|max:40',
        'color_hex'  => 'nullable|hex',
        'price'      => 'nullable|numeric|min:0|max:10000000',
        'sale_price' => 'nullable|numeric|min:0|max:10000000',
        'stock'      => 'required|integer|min:0|max:100000',
    ];

    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['q', 'status', 'category', 'stock']);
        return $this->table($request, 'products/index', 'products/_table', [
            'title'      => 'Products',
            'crumbs'     => [['Products', null]],
            'filters'    => $filters,
            'result'     => (new ProductRepository())->adminList($filters, $this->pageNumber($request)),
            'categories' => (new CategoryRepository())->tree(false),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->form(null);
    }

    public function edit(Request $request): Response
    {
        return $this->form($this->notFoundUnless((new ProductRepository())->find((int) $request->param('id'))));
    }

    private function form(?array $product): Response
    {
        $products = new ProductRepository();
        return $this->page('products/form', [
            'title'      => $product ? 'Edit ' . $product['name'] : 'New product',
            'crumbs'     => [['Products', route('admin.products')], [$product ? $product['name'] : 'New product', null]],
            'product'    => $product,
            'variants'   => $product ? $products->variants((int) $product['id']) : [],
            'images'     => $product ? $products->images((int) $product['id']) : [],
            'categories' => (new CategoryRepository())->tree(false),
            'brands'     => (new BrandRepository())->withCounts(),
            'maxMb'      => (int) (config('app.uploads.max_bytes') / 1048576),
        ]);
    }

    public function store(Request $request): Response
    {
        [$data, $variants] = $this->validated($request, 0);
        $id = Database::transaction(function () use ($data, $variants): int {
            $id = (new ProductRepository())->create($data);
            (new VariantRepository())->sync($id, $variants);
            return $id;
        });
        $warnings = $this->handleImages($request, $id, $data['name']);
        ActivityLogger::log('product.created', 'product', $id, "Created product {$data['name']}", ['status' => $data['status']]);
        $this->warn($warnings);
        flash('success', "{$data['name']} created" . ($data['status'] === 'draft' ? ' as a draft.' : ' and published.'));
        return $this->redirect(route('admin.products.edit', ['id' => $id]));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $products = new ProductRepository();
        $before = $this->notFoundUnless($products->find($id));
        [$data, $variants] = $this->validated($request, $id);
        Database::transaction(function () use ($products, $id, $data, $variants): void {
            $products->update($id, $data);
            (new VariantRepository())->sync($id, $variants);
        });
        $warnings = $this->handleImages($request, $id, $data['name']);
        $changed = array_keys(array_filter($data, static fn ($v, $k) => (string) ($before[$k] ?? '') !== (string) $v, ARRAY_FILTER_USE_BOTH));
        ActivityLogger::log('product.updated', 'product', $id, "Updated product {$data['name']}", ['fields' => $changed]);
        $this->warn($warnings);
        flash('success', 'Changes saved.');
        return $this->redirect(route('admin.products.edit', ['id' => $id]));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $products = new ProductRepository();
        $product = $this->notFoundUnless($products->find($id));
        $products->softDelete($id);
        ActivityLogger::log('product.deleted', 'product', $id, "Deleted product {$product['name']}");
        flash('success', "{$product['name']} deleted. Past orders keep their copy of it.");
        return $this->redirect(route('admin.products'));
    }

    public function bulk(Request $request): Response
    {
        $ids = $this->ids($request);
        $action = (string) $request->input('action');
        if (!$ids || !in_array($action, ['publish', 'draft', 'delete'], true)) {
            return $this->fail($request, 'Choose at least one product and an action.', route('admin.products'));
        }
        $products = new ProductRepository();
        $names = $products->namesByIds($ids);
        if ($action === 'delete') {
            foreach ($ids as $id) {
                $products->softDelete($id);
            }
        } else {
            $products->setStatus($ids, $action === 'publish' ? 'published' : 'draft');
        }
        ActivityLogger::log("product.bulk_{$action}", 'product', null, ucfirst($action) . ' ' . plural(count($names), 'product') . ': ' . str_limit(implode(', ', $names), 180), ['ids' => $ids]);
        $verb = ['publish' => 'published', 'draft' => 'moved to drafts', 'delete' => 'deleted'][$action];
        return $this->done($request, plural(count($names), 'product') . " {$verb}.", route('admin.products'));
    }

    /** @return array{0: array, 1: array} product columns and variant rows */
    private function validated(Request $request, int $productId): array
    {
        $input = $request->all();
        if (trim((string) ($input['slug'] ?? '')) === '' && !empty($input['name'])) {
            $input['slug'] = slugify((string) $input['name']);
        }
        $errors = [];
        try {
            $data = Validator::validate($input, self::RULES, ['category_id' => 'a category', 'short_description' => 'a one-line summary']);
        } catch (ValidationException $e) {
            $errors = $e->errors;
            $data = [];
        }

        if ($data) {
            if ($data['sale_price'] !== null && $data['sale_price'] >= $data['price']) {
                $errors['sale_price'] = 'The sale price must be lower than the regular price.';
            }
            if ($data['sale_starts_at'] && $data['sale_ends_at'] && $data['sale_ends_at'] <= $data['sale_starts_at']) {
                $errors['sale_ends_at'] = 'The sale must end after it starts.';
            }
            if ((new ProductRepository())->slugExists($data['slug'], $productId)) {
                $errors['slug'] = 'Another product already uses this web address.';
            }
            if (!(new CategoryRepository())->find($data['category_id'])) {
                $errors['category_id'] = 'Choose a category.';
            }
            if ($data['brand_id'] !== null && !(new BrandRepository())->find($data['brand_id'])) {
                $errors['brand_id'] = 'Choose a maker from the list.';
            }
        }

        [$variants, $variantError] = $this->validatedVariants((array) ($input['variants'] ?? []), $productId);
        if ($variantError !== null) {
            $errors['variants'] = $variantError;
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
        $data['is_featured'] = $data['is_featured'] ? 1 : 0;
        return [$data, $variants];
    }

    /** @return array{0: array, 1: ?string} */
    private function validatedVariants(array $rows, int $productId): array
    {
        $rows = array_values(array_filter($rows, 'is_array'));
        if (!$rows) {
            return [[], 'Add at least one variant. A product with no options still needs one row for its SKU and stock.'];
        }
        if (count($rows) > 60) {
            return [[], 'A product can have at most 60 variants.'];
        }
        $clean = [];
        $skus = [];
        $combos = [];
        $variantRepo = new VariantRepository();
        $owned = array_map('intval', array_column((new ProductRepository())->variants($productId), 'id'));
        foreach ($rows as $i => $row) {
            $n = $i + 1;
            try {
                $v = Validator::validate($row, self::VARIANT_RULES, ['sku' => 'a SKU', 'stock' => 'stock']);
            } catch (ValidationException $e) {
                return [[], "Variant row {$n}: " . reset($e->errors)];
            }
            $v['sku'] = strtoupper($v['sku']);
            $v['id'] = in_array((int) $v['id'], $owned, true) ? (int) $v['id'] : null;
            $combo = mb_strtolower(($v['size'] ?? '') . '|' . ($v['color'] ?? ''));
            if (isset($skus[$v['sku']])) {
                return [[], "Variant row {$n}: SKU {$v['sku']} is used twice."];
            }
            if (isset($combos[$combo])) {
                return [[], "Variant row {$n}: another row already has this size and colour."];
            }
            if ($variantRepo->skuTaken($v['sku'], $v['id'] ?? 0)) {
                return [[], "Variant row {$n}: SKU {$v['sku']} belongs to another product."];
            }
            if ($v['sale_price'] !== null && $v['price'] !== null && $v['sale_price'] >= $v['price']) {
                return [[], "Variant row {$n}: the sale price must be lower than the price."];
            }
            if ($v['color'] === null) {
                $v['color_hex'] = null;
            }
            $skus[$v['sku']] = true;
            $combos[$combo] = true;
            $clean[] = $v;
        }
        return [$clean, null];
    }

    /** Removes, reorders and adds images. Upload problems don't lose the rest of the save; they come back as warnings. */
    private function handleImages(Request $request, int $productId, string $name): array
    {
        $images = new ProductImageRepository();
        $uploader = new ImageUploader();
        $remove = array_map('intval', (array) $request->input('remove_images', []));
        $uploader->delete(...$images->remove($productId, $remove));
        $order = array_values(array_diff(array_map('intval', (array) $request->input('image_order', [])), $remove));
        $images->reorder($productId, $order);

        $warnings = [];
        foreach (array_slice($request->files('images'), 0, 12) as $file) {
            try {
                $stored = $uploader->store($file, 'products', 1400, 480);
                $images->add($productId, $stored['path'], $stored['thumb'], $name);
            } catch (UploadException $e) {
                $warnings[] = basename((string) $file['name']) . ': ' . $e->getMessage();
            }
        }
        return $warnings;
    }

    private function warn(array $warnings): void
    {
        if ($warnings) {
            \App\Core\Session::flash('upload_warnings', $warnings);
        }
    }
}
