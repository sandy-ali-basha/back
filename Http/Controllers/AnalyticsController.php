<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // -------------------
        // 1) Order Status Data
        // -------------------
        $totalOrders = DB::table('lunar_carts')->count();
   $completedCarts = DB::table('lunar_carts')
    ->whereNotNull('completed_at')
    ->count();
    $abandonedCart = DB::table('lunar_carts')
    ->whereNull('completed_at')
    ->count();

        $orderStatusData = compact('totalOrders','completedCarts','abandonedCart');

        // -------------------
        // 2) Product Performance Data (Top 10 Products)
        // -------------------
        $topProducts = DB::table('lunar_order_lines')
            ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                        ->join('lunar_carts', 'lunar_orders.id', '=', 'lunar_carts.order_id')

            ->select(
                'purchasable_id',
                DB::raw('SUM(lunar_order_lines.quantity) as unitsSold'),
                DB::raw('lunar_order_lines.sub_total as orderValue'),
            )
            ->where('lunar_carts.completed_at', "!=",'null')
            ->groupBy(['lunar_order_lines.purchasable_id','lunar_order_lines.quantity','lunar_order_lines.sub_total'])
            ->orderByDesc('unitsSold')
            ->limit(10)
            ->get()
            ->map(function($row){
                $product = DB::table('lunar_products_variants')->where('id', $row->purchasable_id)->first();
                return [
                    'name' => $product ? $product->name : 'Unknown',
                    'unitsSold' => (int)$row->unitsSold,
                    'conversionRate' => (float) rand(1,10), // placeholder
                    'orderValue' => (float) round($row->orderValue, 2)                ];
            });

        $productPerformanceData = ['products' => $topProducts];

        // -------------------
        // 3) Abandoned Cart Data
        // -------------------
 $abandonedCarts = DB::table('lunar_carts')
    ->whereNull('completed_at')
    ->count();
        $totalCarts = DB::table('lunar_carts')->count() ?: 1;
        $abandonedCartRate = round(($abandonedCarts / $totalCarts) * 100, 2);

    $points = DB::table('lunar_carts')
    ->select(
        DB::raw("CASE 
            WHEN completed_at IS NULL THEN 'abandoned' 
            ELSE 'completed' 
        END AS cart_status"),
        DB::raw('COUNT(*) as cnt')
    )
    ->groupBy('cart_status')
    ->pluck('cnt', 'cart_status')
    ->toArray();

        $abandonedCartData = [
            'abandonedCarts' => (int)$abandonedCarts,
            'abandonedCartRate' => (float)$abandonedCartRate,
            'pointOfAbandonment' => [
                'shipping' => (int)($points['shipping'] ?? 0),
                'payment' => (int)($points['payment'] ?? 0),
                'checkout' => (int)($points['checkout'] ?? 0),
            ]
        ];


        // -------------------
        // 5) User Behavior Data
        // -------------------
        // Return all data
        // -------------------
        return response()->json([
            'orderStatusData' => $orderStatusData,
            'productPerformanceData' => $productPerformanceData,
            'abandonedCartData' => $abandonedCartData,
    
        ]);
    }
}
