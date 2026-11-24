<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\ValidationException;
use App\Services\ActivityLogger;
use App\Services\ImageUploader;
use App\Services\Settings;
use App\Services\UploadException;

final class SettingController extends AdminController
{
    private const RULES = [
        'store_name'              => 'required|string|max:60',
        'store_tagline'           => 'nullable|string|max:160',
        'currency_code'           => 'required|in:NPR,INR,USD',
        'currency_symbol'         => 'required|string|max:6',
        'tax_rate'                => 'required|numeric|min:0|max:50',
        'tax_label'               => 'required|string|max:20',
        'shipping_fee_valley'     => 'required|numeric|min:0|max:100000',
        'shipping_fee_outside'    => 'required|numeric|min:0|max:100000',
        'free_shipping_threshold' => 'nullable|numeric|min:0|max:10000000',
        'valley_districts'        => 'required|string|max:255',
        'pickup_enabled'          => 'boolean',
        'pickup_address'          => 'nullable|string|max:255',
        'support_email'           => 'required|email',
        'support_phone'           => 'required|string|max:30',
        'low_stock_threshold'     => 'required|integer|min:0|max:1000',
    ];

    public function edit(Request $request): Response
    {
        return $this->page('settings/edit', [
            'title'    => 'Store settings',
            'crumbs'   => [['Store settings', null]],
            'settings' => Settings::all(),
            'gd'       => ImageUploader::gdAvailable(),
        ]);
    }

    public function update(Request $request): Response
    {
        $data = $this->validate($request, self::RULES, ['store_name' => 'a store name', 'valley_districts' => 'the valley districts']);
        if ($data['pickup_enabled'] && !$data['pickup_address']) {
            throw new ValidationException(['pickup_address' => 'Add the pickup address customers should come to.']);
        }
        $data['pickup_enabled'] = $data['pickup_enabled'] ? '1' : '0';
        $data['free_shipping_threshold'] ??= 0;
        $data['valley_districts'] = implode(',', array_filter(array_map('trim', explode(',', $data['valley_districts']))));

        $before = Settings::all();
        $logo = $request->file('logo');
        if ($logo !== null) {
            $uploader = new ImageUploader();
            try {
                $stored = $uploader->store($logo, 'brand', 600, 300);
            } catch (UploadException $e) {
                throw new ValidationException(['logo' => $e->getMessage()]);
            }
            $uploader->delete($stored['thumb'], $before['logo_path'] ?? null);
            $data['logo_path'] = $stored['path'];
        } elseif ($request->input('remove_logo') === '1') {
            (new ImageUploader())->delete($before['logo_path'] ?? null);
            $data['logo_path'] = '';
        }

        Settings::save($data);
        $changed = array_keys(array_filter($data, static fn ($v, $k) => (string) ($before[$k] ?? '') !== (string) $v, ARRAY_FILTER_USE_BOTH));
        ActivityLogger::log('settings.updated', 'settings', null, $changed ? 'Updated store settings: ' . implode(', ', $changed) : 'Saved store settings (no changes)', ['fields' => $changed]);
        flash('success', 'Settings saved. They apply to new carts and orders straight away.');
        return $this->redirect(route('admin.settings'));
    }
}
