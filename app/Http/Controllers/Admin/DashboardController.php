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

        $discountEnabled = \App\Models\Setting::getValue('discount_enabled', 'true') === 'true';
        $discountPercentage = (int) \App\Models\Setting::getValue('discount_percentage', 10);

        $members = Customer::where('is_member', true)
            ->withCount('orders')
            ->latest()
            ->get();

        $guests = Customer::where('is_member', false)->latest()->get();

        // 1. 7 Days Sales Trend
        $sevenDaysSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->isoFormat('D MMM');
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            $orderCount = Order::whereDate('created_at', $date)->count();
            $sevenDaysSales[] = [
                'label' => $formattedDate,
                'revenue' => (float) $revenue,
                'orders' => $orderCount
            ];
        }

        // 2. Top 5 Selling Products
        $topProducts = OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_sold'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product')
            ->get();

        // 3. 35 Days Activity Heatmap Data
        $heatmapData = [];
        for ($i = 34; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $orderCount = Order::whereDate('created_at', $date)->count();
            $heatmapData[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->isoFormat('dddd'),
                'formatted_date' => $date->isoFormat('D MMMM YYYY'),
                'count' => $orderCount
            ];
        }

        // 4. Monthly Target Status
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyRevenueProgress = (float) Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $monthlyRevenueTarget = (float) \App\Models\Setting::getValue('monthly_revenue_target', 15000000.00);

        // 5. Recent Product Reviews
        $recentReviews = \App\Models\ProductReview::with('product')
            ->latest()
            ->take(4)
            ->get();

        if ($recentReviews->isEmpty()) {
            // Get testimonials as fallback to avoid empty look
            $recentReviews = \App\Models\Testimonial::latest()->take(4)->get()->map(function($item) {
                return (object)[
                    'name' => $item->name,
                    'rating' => $item->rating,
                    'comment' => $item->body,
                    'product' => (object)['name' => 'Ulasan Toko'],
                    'created_at' => $item->created_at
                ];
            });
        }

        return view('admin.dashboard', compact(
            'todayOrders', 'processingOrders', 'productsSold',
            'todayRevenue', 'totalProducts', 'totalCustomers',
            'pendingOrders', 'recentOrders', 'discountEnabled',
            'discountPercentage', 'members', 'guests',
            'sevenDaysSales', 'topProducts', 'heatmapData',
            'monthlyRevenueProgress', 'monthlyRevenueTarget', 'recentReviews'
        ));
    }

    public function updateRevenueTarget(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'monthly_revenue_target' => 'required|numeric|min:0',
        ]);

        \App\Models\Setting::setValue('monthly_revenue_target', (string) $request->monthly_revenue_target);

        return redirect()->route('admin.dashboard')->with('success', 'Target omset bulanan berhasil diperbarui!');
    }

    public function updateCustomerPoints(\Illuminate\Http\Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:0',
        ]);

        $customer->update([
            'points' => $request->points,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', "Poin member {$customer->name} berhasil diperbarui!");
    }

    public function resetMemberPoints(Customer $customer)
    {
        $customer->update([
            'points' => 0,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', "Poin member {$customer->name} berhasil direset ke 0!");
    }

    public function deleteMember(Customer $customer)
    {
        $name = $customer->name;
        if ($customer->orders()->count() > 0) {
            $customer->update([
                'is_member' => false,
                'points' => 0,
            ]);
            return redirect()->route('admin.dashboard', ['tab' => 'member'])
                ->with('success', "Member {$name} memiliki riwayat pesanan. Status keanggotaan telah dihapus dan poin direset ke 0 demi menjaga integritas data transaksi.");
        }

        $customer->delete();
        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', "Member {$name} berhasil dihapus sepenuhnya.");
    }

    public function toggleCustomerMember(Customer $customer)
    {
        $customer->update([
            'is_member' => !$customer->is_member,
        ]);

        $status = $customer->is_member ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', "Status keanggotaan {$customer->name} berhasil {$status}!");
    }

    public function addMember(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'nullable|integer|min:0',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->update([
            'is_member' => true,
            'points' => $request->points ?? 0,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', "{$customer->name} berhasil bergabung sebagai member!");
    }

    public function updateMemberDiscount(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'discount_percentage' => 'required|integer|min:1|max:100',
        ]);

        \App\Models\Setting::setValue('discount_enabled', $request->has('discount_enabled') ? 'true' : 'false');
        \App\Models\Setting::setValue('discount_percentage', (string) $request->discount_percentage);

        return redirect()->route('admin.dashboard', ['tab' => 'member'])->with('success', 'Pengaturan diskon member berhasil diperbarui!');
    }

    public function checkNewOrders(\Illuminate\Http\Request $request)
    {
        $lastOrderId = $request->get('last_order_id', 0);
        $lastPollTime = $request->get('last_poll_time');

        $query = Order::query();

        if ($lastPollTime) {
            $parsedTime = Carbon::parse($lastPollTime);
            $query->where(function ($q) use ($parsedTime) {
                $q->where('updated_at', '>', $parsedTime)
                    ->where(function ($sub) {
                        $sub->where(function ($sub2) {
                            $sub2->where('payment_status', 'paid')
                                 ->whereIn('status', ['pending', 'confirmed']);
                        })->orWhere(function ($sub2) {
                            $sub2->whereIn('payment_method', ['Cash On Delivery / COD', 'WhatsApp Confirmation'])
                                 ->whereIn('status', ['pending', 'confirmed']);
                        });
                    });
            });
        } else {
            $query->where('id', '>', $lastOrderId)
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('payment_status', 'paid')
                            ->whereIn('status', ['pending', 'confirmed']);
                    })->orWhere(function ($sub) {
                        $sub->whereIn('payment_method', ['Cash On Delivery / COD', 'WhatsApp Confirmation'])
                            ->whereIn('status', ['pending', 'confirmed']);
                    });
                });
        }

        $newOrdersCount = $query->count();
            
        return response()->json([
            'new_orders_count' => $newOrdersCount,
            'latest_order_id' => Order::max('id') ?: 0,
            'latest_poll_time' => now()->toIso8601String()
        ]);
    }
}
