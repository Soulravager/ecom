<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{  
    public function stats()
    {
        return response()->json([
            'total_users'  => User::count(),
            'total_orders' => Order::count(),
            'total_sales'  => Order::sum('total_amount'),
            'net_stock'    => Product::sum('stock'),
        ]);
    }

    public function lowStock()
    {
        $lowStockProducts = Product::where('stock', '<', 40)->get();

        return response()->json([
            'low_stock_products' => $lowStockProducts
        ]);
    }


public function salesStats(Request $request)
{
    $request->validate([
        'from_date'   => 'required|date',
        'to_date'     => 'required|date',
        'product_id'  => 'nullable|exists:products,id',
    ]);

    $query = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            'order_items.product_id',
            'products.name as product_name', 
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('COUNT(order_items.id) as purchase_count')
        )
        ->whereBetween('orders.created_at', [$request->from_date, $request->to_date])
        ->groupBy('order_items.product_id', 'products.name')
        ->orderByDesc('total_quantity');

    if ($request->product_id) {
        $query->where('order_items.product_id', $request->product_id);
    }

    $results = $query->get();

    return response()->json([
        'sales_stats' => $results
    ]);
}







}
