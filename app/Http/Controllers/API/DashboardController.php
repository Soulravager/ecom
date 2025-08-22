<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

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
}

