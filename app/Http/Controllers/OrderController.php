<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $products = Product::with('category')
            ->where('is_available', true)
            ->get();

        $categories = \App\Models\Category::where('is_active', true)->get();
        $storeWhatsapp = Setting::getValue('store_whatsapp');
        $canOrder = Setting::canAcceptOrder();
        $googleMapsKey = Setting::getValue('google_maps_api_key', '');
        $storeLat = Setting::getValue('store_latitude', '-7.7705163');
        $storeLng = Setting::getValue('store_longitude', '110.2474903');
        $deliveryFeeEnabled = Setting::getValue('delivery_fee_enabled', 'true') === 'true';
        $deliveryFeeAmount = (int) Setting::getValue('delivery_fee_amount', 10000);

        return view('order.create', compact('products', 'categories', 'storeWhatsapp', 'canOrder', 'googleMapsKey', 'storeLat', 'storeLng', 'deliveryFeeEnabled', 'deliveryFeeAmount'));
    }

    public function store(Request $request)
    {
        if (!Setting::canAcceptOrder()) {
            return back()->with('error', 'Maaf, toko sedang tutup atau kuota pesanan hari ini penuh.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'type' => 'required|in:pickup,delivery',
            'pickup_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['type'] === 'delivery' ? $validated['address'] : null,
        ]);

        $total = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'order_date' => now(),
            'pickup_date' => $validated['pickup_date'],
            'type' => $validated['type'],
            'status' => 'pending',
            'notes' => $validated['notes'],
            'total' => $total,
            'address' => $validated['type'] === 'delivery' ? $validated['address'] : null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'google_maps_link' => ($validated['latitude'] ?? null) && ($validated['longitude'] ?? null)
                ? "https://www.google.com/maps?q={$validated['latitude']},{$validated['longitude']}"
                : null,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        $order->kitchenTask()->create([
            'status' => 'pending',
        ]);

        $whatsappUrl = $this->generateWhatsappUrl($order);

        return redirect()->route('order.success', $order->id)
            ->with('whatsapp_url', $whatsappUrl);
    }

    public function success($id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($id);
        $whatsappUrl = session('whatsapp_url');
        $storeWhatsapp = Setting::getValue('store_whatsapp');

        return view('order.success', compact('order', 'whatsappUrl', 'storeWhatsapp'));
    }

    public function statusForm()
    {
        return view('order.status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate(['phone' => 'required|string|max:20']);

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return back()->with('error', 'Nomor WhatsApp tidak ditemukan. Pastikan nomor yang digunakan saat order.');
        }

        $orders = Order::with('items.product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('order.status', compact('orders', 'customer'));
    }

    private function generateWhatsappUrl(Order $order)
    {
        $phone = Setting::getValue('store_whatsapp');
        $message = "Halo Mamitha Bakery! Saya ingin konfirmasi pesanan:\n\n";
        $message .= "No. Pesanan: {$order->order_number}\n";
        $message .= "Nama: {$order->customer->name}\n";
        $message .= "Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\n";

        foreach ($order->items as $item) {
            $message .= "- {$item->product->name} x {$item->quantity}\n";
        }

        $message .= "\nTerima kasih.";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
}
