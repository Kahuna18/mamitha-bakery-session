<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $processingOrders = Order::whereIn('status', ['confirmed', 'producing'])->count();
        $productsSold = OrderItem::whereDate('created_at', $today)->sum('quantity');
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $recentOrders = Order::with('customer')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders', 'processingOrders', 'productsSold',
            'todayRevenue', 'totalProducts', 'totalCustomers',
            'pendingOrders', 'recentOrders'
        ));
    }
}
