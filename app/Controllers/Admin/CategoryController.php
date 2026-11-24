<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Core\Validator;
use App\Models\CategoryRepository;
use App\Services\ActivityLogger;
use App\Services\ImageUploader;
use App\Services\UploadException;

final class CategoryController extends AdminController
{
    private const RULES = [
        'name'        => 'required|string|min:2|max:100',
        'slug'        => 'nullable|slug|max:120',
        'parent_id'   => 'nullable|integer',
        'tagline'     => 'nullable|string|max:160',
        'description' => 'nullable|string|max:2000',
        'sort_order'  => 'nullable|integer|min:0|max:999',
        'is_active'   => 'boolean',
    ];

    public function index(Request $request): Response
    {
        return $this->page('catalog/categories', [
            'title'  => 'Categories',
            'crumbs' => [['Categories', null]],
            'tree'   => (new CategoryRepository())->tree(false),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validated($request, 0);
        $id = (new CategoryRepository())->create($data);
        $this->storeImage($request, $id);
        ActivityLogger::log('category.created', 'category', $id, "Created category {$data['name']}");
        return $this->done($request, "Category {$data['name']} added.", route('admin.categories'));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new CategoryRepository();
        $this->notFoundUnless($repo->find($id));
        $data = $this->validated($request, $id);
        $repo->update($id, $data);
        $this->storeImage($request, $id);
        ActivityLogger::log('category.updated', 'category', $id, "Updated category {$data['name']}");
        return $this->done($request, "Category {$data['name']} saved.", route('admin.categories'));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new CategoryRepository();
        $category = $this->notFoundUnless($repo->find($id));
        if ($repo->isInUse($id)) {
            return $this->fail($request, "{$category['name']} still has products or sub-categories. Move them first, or hide the category instead.", route('admin.categories'));
        }
        $repo->delete($id);
        (new ImageUploader())->delete($category['image_path']);
        ActivityLogger::log('category.deleted', 'category', $id, "Deleted category {$category['name']}");
        return $this->done($request, "Category {$category['name']} deleted.", route('admin.categories'));
    }

    private function validated(Request $request, int $id): array
    {
        $input = $request->all();
        if (trim((string) ($input['slug'] ?? '')) === '' && !empty($input['name'])) {
            $input['slug'] = slugify((string) $input['name']);
        }
        $data = Validator::validate($input, self::RULES, ['name' => 'a name']);
        $repo = new CategoryRepository();
        $errors = [];
        if ($repo->slugExists($data['slug'], $id)) {
            $errors['slug'] = 'Another category already uses this web address.';
        }
        if ($data['parent_id'] !== null) {
            $parent = $repo->find($data['parent_id']);
            // Two levels keep menus readable; a category can't be its own parent either.
            if (!$parent || $parent['parent_id'] !== null || $data['parent_id'] === $id) {
                $errors['parent_id'] = 'Choose a top-level category as the parent.';
            } elseif ($id && $repo->hasChildren($id)) {
                $errors['parent_id'] = 'This category has its own sub-categories, so it must stay top-level.';
            }
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $data['is_active'] ? 1 : 0;
        return $data;
    }

    private function storeImage(Request $request, int $id): void
    {
        $file = $request->file('image');
        if ($file === null) {
            return;
        }
        $repo = new CategoryRepository();
        $uploader = new ImageUploader();
        try {
            $stored = $uploader->store($file, 'categories', 1200, 600);
        } catch (UploadException $e) {
            throw new ValidationException(['image' => $e->getMessage()]);
        }
        $uploader->delete($repo->find($id)['image_path'] ?? null);
        $uploader->delete($stored['thumb']);
        $repo->update($id, ['image_path' => $stored['path']]);
    }
}
