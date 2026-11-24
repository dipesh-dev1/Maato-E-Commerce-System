<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\OrderRepository;
use App\Models\ProductRepository;
use App\Models\ReviewRepository;
use App\Models\UserRepository;

final class DashboardController extends AdminController
{
    private const RANGES = [7, 30, 90];

    public function index(Request $request): Response
    {
        $orders = new OrderRepository();
        $products = new ProductRepository();
        $threshold = (int) setting('low_stock_threshold', 5);
        $today = date('Y-m-d 00:00:00');
        $week = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $month = date('Y-m-01 00:00:00');
        $last30 = date('Y-m-d 00:00:00', strtotime('-29 days'));
        $prev30 = date('Y-m-d 00:00:00', strtotime('-59 days'));

        $revenue30 = $orders->revenueSince($last30);
        $revenuePrev = $orders->revenueSince($prev30) - $revenue30;

        return $this->page('dashboard', [
            'title'   => 'Dashboard',
            'kpis'    => [
                'revenue_30'   => $revenue30,
                'revenue_change' => $revenuePrev > 0 ? ($revenue30 - $revenuePrev) / $revenuePrev * 100 : null,
                'revenue_today'=> $orders->revenueSince($today),
                'orders_today' => $orders->countSince($today),
                'orders_week'  => $orders->countSince($week),
                'orders_month' => $orders->countSince($month),
                'new_customers'=> (new UserRepository())->countSince($last30),
                'low_stock'    => $products->countLowStock($threshold),
                'pending_reviews' => (new ReviewRepository())->pendingCount(),
            ],
            'statusCounts' => $orders->countByStatus(),
            'series'  => $orders->dailySales(30),
            'top'     => $orders->topProducts(30),
            'recent'  => $orders->recent(6),
            'lowStock'=> $products->lowStockVariants($threshold),
            'threshold' => $threshold,
        ]);
    }

    /** Chart data for the range switcher. */
    public function sales(Request $request): Response
    {
        $days = (int) $request->query('days', 30);
        $days = in_array($days, self::RANGES, true) ? $days : 30;
        $series = (new OrderRepository())->dailySales($days);
        return $this->json([
            'series' => array_map(static fn ($p) => $p + ['label' => money($p['revenue'])], $series),
            'total'  => money(array_sum(array_column($series, 'revenue'))),
            'orders' => array_sum(array_column($series, 'orders')),
        ]);
    }
}
