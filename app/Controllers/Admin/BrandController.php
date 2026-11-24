<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Core\Validator;
use App\Models\BrandRepository;
use App\Services\ActivityLogger;

final class BrandController extends AdminController
{
    private const RULES = [
        'name'        => 'required|string|min:2|max:100',
        'slug'        => 'nullable|slug|max:120',
        'origin'      => 'nullable|string|max:100',
        'description' => 'nullable|string|max:2000',
        'is_active'   => 'boolean',
    ];

    public function index(Request $request): Response
    {
        return $this->page('catalog/brands', [
            'title'  => 'Makers & brands',
            'crumbs' => [['Makers & brands', null]],
            'brands' => (new BrandRepository())->withCounts(),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validated($request, 0);
        $id = (new BrandRepository())->create($data);
        ActivityLogger::log('brand.created', 'brand', $id, "Created maker {$data['name']}");
        return $this->done($request, "{$data['name']} added.", route('admin.brands'));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new BrandRepository();
        $this->notFoundUnless($repo->find($id));
        $data = $this->validated($request, $id);
        $repo->update($id, $data);
        ActivityLogger::log('brand.updated', 'brand', $id, "Updated maker {$data['name']}");
        return $this->done($request, "{$data['name']} saved.", route('admin.brands'));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new BrandRepository();
        $brand = $this->notFoundUnless($repo->find($id));
        $repo->delete($id);
        ActivityLogger::log('brand.deleted', 'brand', $id, "Deleted maker {$brand['name']}");
        return $this->done($request, "{$brand['name']} deleted. Their products now show no maker.", route('admin.brands'));
    }

    private function validated(Request $request, int $id): array
    {
        $input = $request->all();
        if (trim((string) ($input['slug'] ?? '')) === '' && !empty($input['name'])) {
            $input['slug'] = slugify((string) $input['name']);
        }
        $data = Validator::validate($input, self::RULES, ['name' => 'a name']);
        if ((new BrandRepository())->slugExists($data['slug'], $id)) {
            throw new ValidationException(['slug' => 'Another maker already uses this web address.']);
        }
        $data['is_active'] = $data['is_active'] ? 1 : 0;
        return $data;
    }
}
