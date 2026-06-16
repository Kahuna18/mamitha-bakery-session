<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(20);
        return view('admin.customers', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $orders = $customer->orders()->with('items.product')->latest()->get();
        return view('admin.customer-detail', compact('customer', 'orders'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pelanggan yang memiliki riwayat pesanan.');
        }

        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function resetAll()
    {
        $deleted = Customer::whereDoesntHave('orders')->delete();

        return redirect()->route('admin.customers')->with('success', "{$deleted} pelanggan tanpa riwayat pesanan berhasil direset.");
    }
}
