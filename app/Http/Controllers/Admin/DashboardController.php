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

        return view('admin.dashboard', compact(
            'todayOrders', 'processingOrders', 'productsSold',
            'todayRevenue', 'totalProducts', 'totalCustomers',
            'pendingOrders', 'recentOrders', 'discountEnabled',
            'discountPercentage', 'members', 'guests'
        ));
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
        
        $newOrdersCount = Order::where('id', '>', $lastOrderId)
            ->where('status', 'pending')
            ->count();
            
        return response()->json([
            'new_orders_count' => $newOrdersCount,
            'latest_order_id' => Order::max('id') ?: 0
        ]);
    }
}
