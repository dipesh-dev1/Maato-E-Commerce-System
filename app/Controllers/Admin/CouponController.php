<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Core\Validator;
use App\Models\CouponRepository;
use App\Services\ActivityLogger;

final class CouponController extends AdminController
{
    private const RULES = [
        'code'           => 'required|alpha_code',
        'description'    => 'nullable|string|max:160',
        'type'           => 'required|in:percent,fixed',
        'value'          => 'required|numeric|min:0.01|max:1000000',
        'min_cart_value' => 'nullable|numeric|min:0|max:10000000',
        'usage_limit'    => 'nullable|integer|min:1|max:1000000',
        'starts_at'      => 'nullable|date',
        'expires_at'     => 'nullable|date',
        'is_active'      => 'boolean',
    ];

    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['q', 'state']);
        return $this->table($request, 'coupons/index', 'coupons/_table', [
            'title'   => 'Coupons',
            'crumbs'  => [['Coupons', null]],
            'filters' => $filters,
            'result'  => (new CouponRepository())->adminList($filters, $this->pageNumber($request)),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validated($request, 0);
        $id = (new CouponRepository())->create($data);
        ActivityLogger::log('coupon.created', 'coupon', $id, "Created coupon {$data['code']}", ['type' => $data['type'], 'value' => $data['value']]);
        return $this->done($request, "Coupon {$data['code']} created.", route('admin.coupons'));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new CouponRepository();
        $this->notFoundUnless($repo->find($id));
        $data = $this->validated($request, $id);
        $repo->update($id, $data);
        ActivityLogger::log('coupon.updated', 'coupon', $id, "Updated coupon {$data['code']}", ['type' => $data['type'], 'value' => $data['value']]);
        return $this->done($request, "Coupon {$data['code']} saved.", route('admin.coupons'));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $repo = new CouponRepository();
        $coupon = $this->notFoundUnless($repo->find($id));
        $repo->delete($id);
        ActivityLogger::log('coupon.deleted', 'coupon', $id, "Deleted coupon {$coupon['code']}");
        return $this->done($request, "Coupon {$coupon['code']} deleted. Orders that used it keep the discount.", route('admin.coupons'));
    }

    private function validated(Request $request, int $id): array
    {
        $data = Validator::validate($request->all(), self::RULES, ['code' => 'a code', 'value' => 'a discount value']);
        $data['code'] = strtoupper($data['code']);
        $errors = [];
        if ((new CouponRepository())->codeExists($data['code'], $id)) {
            $errors['code'] = 'That code already exists.';
        }
        if ($data['type'] === 'percent' && $data['value'] > 100) {
            $errors['value'] = 'A percentage discount can be at most 100.';
        }
        if ($data['starts_at'] && $data['expires_at'] && $data['expires_at'] <= $data['starts_at']) {
            $errors['expires_at'] = 'The code must expire after it starts.';
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
        $data['min_cart_value'] = $data['min_cart_value'] ?? 0;
        $data['is_active'] = $data['is_active'] ? 1 : 0;
        return $data;
    }
}
