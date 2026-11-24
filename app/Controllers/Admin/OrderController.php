<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\OrderRepository;
use App\Models\OrderStatus;
use App\Services\ActivityLogger;
use App\Services\CartException;
use App\Services\OrderService;

final class OrderController extends AdminController
{
    private const FILTERS = ['q', 'status', 'payment', 'from', 'to'];

    public function index(Request $request): Response
    {
        $filters = $this->filters($request, self::FILTERS);
        $orders = new OrderRepository();
        return $this->table($request, 'orders/index', 'orders/_table', [
            'title'   => 'Orders',
            'crumbs'  => [['Orders', null]],
            'filters' => $filters,
            'result'  => $orders->adminList($filters, $this->pageNumber($request)),
            'counts'  => $orders->countByStatus(),
        ]);
    }

    public function show(Request $request): Response
    {
        $orders = new OrderRepository();
        $order = $this->notFoundUnless($orders->find((int) $request->param('id')));
        return $this->page('orders/show', [
            'title'   => 'Order ' . $order['order_number'],
            'crumbs'  => [['Orders', route('admin.orders')], [$order['order_number'], null]],
            'order'   => $order,
            'items'   => $orders->items((int) $order['id']),
            'history' => $orders->history((int) $order['id']),
            'next'    => OrderStatus::TRANSITIONS[$order['status']],
        ]);
    }

    public function status(Request $request): Response
    {
        $id = (int) $request->param('id');
        $data = $this->validate($request, [
            'status' => 'required|in:' . implode(',', array_keys(OrderStatus::LABELS)),
            'note'   => 'nullable|string|max:500',
        ], ['status' => 'the new status']);
        try {
            $order = (new OrderService())->changeStatus($id, $data['status'], $data['note'], (int) Auth::id());
        } catch (CartException $e) {
            return $this->fail($request, $e->getMessage(), route('admin.orders.show', ['id' => $id]));
        }
        ActivityLogger::log('order.status', 'order', $id, sprintf(
            'Moved order %s from %s to %s',
            $order['order_number'],
            OrderStatus::label($order['status']),
            OrderStatus::label($data['status'])
        ), array_filter(['note' => $data['note']]));
        return $this->done($request, "Order {$order['order_number']} is now " . mb_strtolower(OrderStatus::label($data['status'])) . '.', route('admin.orders.show', ['id' => $id]));
    }

    /** Applies one status to many orders; orders that can't make that move are skipped and reported. */
    public function bulk(Request $request): Response
    {
        $ids = $this->ids($request);
        $to = (string) $request->input('action');
        if (!$ids || !in_array($to, ['confirmed', 'packed', 'shipped', 'delivered'], true)) {
            return $this->fail($request, 'Choose at least one order and a status.', route('admin.orders'));
        }
        $service = new OrderService();
        $moved = [];
        $skipped = 0;
        foreach ($ids as $id) {
            try {
                $moved[] = $service->changeStatus($id, $to, 'Bulk update.', (int) Auth::id())['order_number'];
            } catch (CartException) {
                $skipped++;
            }
        }
        if ($moved) {
            ActivityLogger::log('order.bulk_status', 'order', null, 'Marked ' . plural(count($moved), 'order') . ' as ' . OrderStatus::label($to) . ': ' . str_limit(implode(', ', $moved), 160), ['ids' => $ids]);
        }
        $message = plural(count($moved), 'order') . ' marked ' . mb_strtolower(OrderStatus::label($to)) . '.'
            . ($skipped ? ' ' . plural($skipped, 'order') . " skipped because they can't move to that status." : '');
        return $this->done($request, $message, route('admin.orders'));
    }

    public function invoice(Request $request): Response
    {
        $orders = new OrderRepository();
        $order = $this->notFoundUnless($orders->find((int) $request->param('id')));
        ActivityLogger::log('order.invoice', 'order', (int) $order['id'], "Printed invoice for {$order['order_number']}");
        return Response::html(View::capture('admin/orders/invoice', [
            'order' => $order,
            'items' => $orders->items((int) $order['id']),
        ]));
    }

    public function export(Request $request): Response
    {
        $filters = $this->filters($request, self::FILTERS);
        $cursor = (new OrderRepository())->exportCursor($filters);
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads UTF-8 names correctly
        fputcsv($out, ['Order', 'Placed', 'Status', 'Payment', 'Customer', 'Email', 'Phone', 'City', 'District', 'Items', 'Subtotal', 'Discount', 'Delivery', 'VAT', 'Total', 'Coupon']);
        $count = 0;
        while ($row = $cursor->fetch()) {
            $count++;
            fputcsv($out, array_map([$this, 'csvSafe'], [
                $row['order_number'], $row['created_at'], OrderStatus::label($row['status']), OrderStatus::PAYMENT_LABELS[$row['payment_method']],
                $row['customer_name'], $row['customer_email'], $row['ship_phone'], $row['ship_city'], $row['ship_district'], $row['item_count'],
                $row['subtotal'], $row['discount_total'], $row['shipping_total'], $row['tax_total'], $row['grand_total'], $row['coupon_code'],
            ]));
        }
        rewind($out);
        ActivityLogger::log('order.export', 'order', null, 'Exported ' . plural($count, 'order') . ' to CSV', array_filter($filters));
        return (new Response((string) stream_get_contents($out)))
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="maato-orders-' . date('Y-m-d') . '.csv"')
            ->header('Cache-Control', 'no-store');
    }

    /** Neutralises spreadsheet formula injection in exported text cells. */
    private function csvSafe(mixed $value): string
    {
        $value = (string) $value;
        return $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) && !is_numeric($value) ? "'" . $value : $value;
    }
}
