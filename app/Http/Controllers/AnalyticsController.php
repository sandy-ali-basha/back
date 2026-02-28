<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // -------------------
        // 1) Order Status Data
        // -------------------
        $totalOrders = DB::table('lunar_orders')->count();
        $totalCarts = max((int) DB::table('lunar_carts')->count(), 1);

        // In this project, completed_at is not reliable in production, so we
        // treat carts linked to an order as completed.
        $completedCarts = DB::table('lunar_carts')
            ->whereNotNull('order_id')
            ->count();

        $abandonedCart = DB::table('lunar_carts')
            ->whereNull('order_id')
            ->count();

        $orderStatusData = compact('totalOrders', 'completedCarts', 'abandonedCart');

        // -------------------
        // 2) Product Performance Data (Top 10 Products)
        // -------------------
        $ordersWithLinesCount = max(
            DB::table('lunar_order_lines')
                ->distinct('order_id')
                ->count('order_id'),
            1
        );

        $topProducts = DB::table('lunar_order_lines')
            ->select(
                'purchasable_id',
                DB::raw("CONCAT('Product #', purchasable_id) as productName"),
                DB::raw('SUM(quantity) as unitsSold'),
                DB::raw('SUM(sub_total) as orderValue'),
                DB::raw('COUNT(DISTINCT order_id) as ordersWithProduct')
            )
            ->groupBy('purchasable_id')
            ->orderByDesc('unitsSold')
            ->limit(10)
            ->get()
            ->map(function ($row) use ($ordersWithLinesCount) {
                return [
                    'name' => $row->productName,
                    'unitsSold' => (int) $row->unitsSold,
                    'conversionRate' => (float) round(($row->ordersWithProduct / $ordersWithLinesCount) * 100, 2),
                    'orderValue' => (float) round($row->orderValue, 2),
                ];
            });

        $productPerformanceData = ['products' => $topProducts];

        // -------------------
        // 3) Abandoned Cart Data
        // -------------------
        $abandonedCarts = $abandonedCart;
        $abandonedCartRate = round(($abandonedCarts / $totalCarts) * 100, 2);

        $pointOfAbandonment = [
            'shipping' => 0,
            'payment' => 0,
            'checkout' => (int) $abandonedCarts,
        ];

        $abandonedCartData = [
            'abandonedCarts' => (int) $abandonedCarts,
            'abandonedCartRate' => (float) $abandonedCartRate,
            'pointOfAbandonment' => $pointOfAbandonment,
        ];

        // -------------------
        // 4) Sales Overview Data
        // -------------------
        $sales = DB::table('lunar_order_lines')
            ->select(
                DB::raw('SUM(sub_total) as grossRevenue'),
                DB::raw('SUM(quantity) as totalUnits'),
                DB::raw('COUNT(DISTINCT order_id) as ordersCount')
            )
            ->first();

        $ordersCount = max((int) ($sales->ordersCount ?? 0), 1);
        $grossRevenue = (float) ($sales->grossRevenue ?? 0);
        $totalUnits = (int) ($sales->totalUnits ?? 0);

        $salesOverviewData = [
            'grossRevenue' => (float) round($grossRevenue, 2),
            'averageOrderValue' => (float) round($grossRevenue / $ordersCount, 2),
            'unitsPerOrder' => (float) round($totalUnits / $ordersCount, 2),
            'cartToOrderRate' => (float) round(($completedCarts / $totalCarts) * 100, 2),
        ];

        // -------------------
        // 5) Orders Trend (last 7 days)
        // -------------------
        $from = Carbon::now()->subDays(6)->startOfDay();

        $trendRows = DB::table('lunar_orders')
            ->where('created_at', '>=', $from)
            ->select(
                DB::raw('DATE(created_at) as orderDate'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('orderDate')
            ->orderBy('orderDate')
            ->get()
            ->keyBy('orderDate');

        $ordersTrendData = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->toDateString();
            $ordersTrendData[] = [
                'date' => $date,
                'orders' => (int) optional($trendRows->get($date))->orders,
            ];
        }

        return response()->json([
            'orderStatusData' => $orderStatusData,
            'productPerformanceData' => $productPerformanceData,
            'abandonedCartData' => $abandonedCartData,
            'salesOverviewData' => $salesOverviewData,
            'ordersTrendData' => $ordersTrendData,
        ]);
    }
}
