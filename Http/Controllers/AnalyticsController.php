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
        $totalCartsCount = DB::table('lunar_carts')->count();

        // -------------------
        // 1) Order Status Data
        // -------------------
        $totalOrders = DB::table('lunar_orders')->count();
        $completedCarts = DB::table('lunar_carts')
            ->whereNotNull('completed_at')
            ->count();
        $abandonedCart = DB::table('lunar_carts')
            ->whereNull('completed_at')
            ->count();

        $orderStatusData = compact('totalOrders', 'completedCarts', 'abandonedCart');

        // -------------------
        // 2) Product Performance Data (Top 10 Products)
        // -------------------
        $completedOrdersCount = max(
            DB::table('lunar_orders')
                ->join('lunar_carts', 'lunar_orders.id', '=', 'lunar_carts.order_id')
                ->whereNotNull('lunar_carts.completed_at')
                ->distinct('lunar_orders.id')
                ->count('lunar_orders.id'),
            1
        );

        $topProducts = DB::table('lunar_order_lines')
            ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
            ->join('lunar_carts', 'lunar_orders.id', '=', 'lunar_carts.order_id')
            ->whereNotNull('lunar_carts.completed_at')
            ->select(
                'lunar_order_lines.purchasable_id',
                DB::raw("CONCAT('Product #', lunar_order_lines.purchasable_id) as productName"),
                DB::raw('SUM(lunar_order_lines.quantity) as unitsSold'),
                DB::raw('SUM(lunar_order_lines.sub_total) as orderValue'),
                DB::raw('COUNT(DISTINCT lunar_order_lines.order_id) as ordersWithProduct')
            )
            ->groupBy('lunar_order_lines.purchasable_id')
            ->orderByDesc('unitsSold')
            ->limit(10)
            ->get()
            ->map(function ($row) use ($completedOrdersCount) {
                return [
                    'name' => $row->productName,
                    'unitsSold' => (int) $row->unitsSold,
                    'conversionRate' => (float) round(($row->ordersWithProduct / $completedOrdersCount) * 100, 2),
                    'orderValue' => (float) round($row->orderValue, 2),
                ];
            });

        $productPerformanceData = ['products' => $topProducts];

        // -------------------
        // 3) Abandoned Cart Data
        // -------------------
        $abandonedCarts = DB::table('lunar_carts')
            ->whereNull('completed_at')
            ->count();
        $totalCarts = max($totalCartsCount, 1);
        $abandonedCartRate = round(($abandonedCarts / $totalCarts) * 100, 2);

        $points = DB::table('lunar_carts')
            ->select(
                DB::raw('SUM(CASE WHEN completed_at IS NULL AND shipping_option IS NULL THEN 1 ELSE 0 END) as shipping'),
                DB::raw('SUM(CASE WHEN completed_at IS NULL AND shipping_option IS NOT NULL AND billing_address_id IS NULL THEN 1 ELSE 0 END) as payment'),
                DB::raw('SUM(CASE WHEN completed_at IS NULL AND billing_address_id IS NOT NULL THEN 1 ELSE 0 END) as checkout')
            )
            ->first();

        $abandonedCartData = [
            'abandonedCarts' => (int) $abandonedCarts,
            'abandonedCartRate' => (float) $abandonedCartRate,
            'pointOfAbandonment' => [
                'shipping' => (int) ($points->shipping ?? 0),
                'payment' => (int) ($points->payment ?? 0),
                'checkout' => (int) ($points->checkout ?? 0),
            ],
        ];

        // -------------------
        // 4) Sales Overview Data
        // -------------------
        $sales = DB::table('lunar_order_lines')
            ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
            ->join('lunar_carts', 'lunar_orders.id', '=', 'lunar_carts.order_id')
            ->whereNotNull('lunar_carts.completed_at')
            ->select(
                DB::raw('SUM(lunar_order_lines.sub_total) as grossRevenue'),
                DB::raw('SUM(lunar_order_lines.quantity) as totalUnits'),
                DB::raw('COUNT(DISTINCT lunar_order_lines.order_id) as completedOrders')
            )
            ->first();

        $completedOrders = max((int) ($sales->completedOrders ?? 0), 1);
        $grossRevenue = (float) ($sales->grossRevenue ?? 0);
        $totalUnits = (int) ($sales->totalUnits ?? 0);

        $salesOverviewData = [
            'grossRevenue' => (float) round($grossRevenue, 2),
            'averageOrderValue' => (float) round($grossRevenue / $completedOrders, 2),
            'unitsPerOrder' => (float) round($totalUnits / $completedOrders, 2),
            'cartToOrderRate' => (float) round(($completedCarts / $totalCarts) * 100, 2),
        ];

        // -------------------
        // 5) Orders Trend (last 7 days)
        // -------------------
        $from = Carbon::now()->subDays(6)->startOfDay();

        $trendRows = DB::table('lunar_orders')
            ->join('lunar_carts', 'lunar_orders.id', '=', 'lunar_carts.order_id')
            ->whereNotNull('lunar_carts.completed_at')
            ->where('lunar_orders.created_at', '>=', $from)
            ->select(
                DB::raw('DATE(lunar_orders.created_at) as orderDate'),
                DB::raw('COUNT(DISTINCT lunar_orders.id) as orders')
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
