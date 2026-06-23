<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('order_date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('order_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('order_date', Carbon::now()->month)
                          ->whereYear('order_date', Carbon::now()->year);
                    break;
            }
        }

        if ($request->filled('tab')) {
            $tab = $request->tab;
            if ($tab === 'pending_payment') {
                $query->where('status', 'pending')
                      ->where('payment_status', 'unpaid')
                      ->where(function($q) {
                          $q->where('payment_method', 'like', '%Transfer Bank%')
                            ->orWhere('payment_method', 'like', '%Online Payment%')
                            ->orWhere('payment_method', 'like', '%Midtrans%')
                            ->orWhere('payment_method', 'like', '%- %');
                      });
            } elseif ($tab === 'kitchen') {
                $query->whereIn('status', ['confirmed', 'producing', 'ready']);
            } elseif ($tab === 'completed') {
                $query->where('status', 'done');
            } elseif ($tab === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest('order_date')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'kitchenTask']);
        $googleMapsKey = Setting::getValue('google_maps_api_key', '');
        return view('admin.orders.show', compact('order', 'googleMapsKey'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,producing,ready,done,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        // Automatically mark manual transfer/QRIS as paid if confirmed or producing/ready/done
        if (in_array($request->status, ['confirmed', 'producing', 'ready', 'done']) && $order->payment_status === 'unpaid') {
            $order->update(['payment_status' => 'paid']);
        }

        if (!$order->kitchenTask) {
            if (in_array($request->status, ['confirmed', 'producing', 'ready', 'done'])) {
                $taskStatus = match ($request->status) {
                    'producing' => 'producing',
                    'ready', 'done' => 'done',
                    default => 'pending',
                };
                $order->kitchenTask()->create([
                    'status' => $taskStatus,
                    'started_at' => $request->status === 'producing' ? now() : (($request->status === 'ready' || $request->status === 'done') ? now() : null),
                    'completed_at' => in_array($request->status, ['ready', 'done']) ? now() : null,
                ]);
            }
        } else {
            $taskStatus = match ($request->status) {
                'producing' => 'producing',
                'ready', 'done' => 'done',
                default => 'pending',
            };
            $order->kitchenTask()->update([
                'status' => $taskStatus,
                'started_at' => $request->status === 'producing' ? now() : $order->kitchenTask->started_at,
                'completed_at' => in_array($request->status, ['ready', 'done']) ? now() : $order->kitchenTask->completed_at,
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product']);
        $storeName = Setting::getValue('store_name');
        $storeAddress = Setting::getValue('store_address');
        $storePhone = Setting::getValue('store_phone');

        return view('admin.orders.invoice', compact('order', 'storeName', 'storeAddress', 'storePhone'));
    }

    public function destroy(Order $order)
    {
        $order->kitchenTask()->delete();
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
