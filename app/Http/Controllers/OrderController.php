<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $products = Product::with(['category', 'activeVariants', 'reviews'])
            ->where('is_available', true)
            ->get();

        $categories = \App\Models\Category::where('is_active', true)->get();
        $storeWhatsapp = Setting::getValue('store_whatsapp');
        $canOrder = Setting::canAcceptOrder();
        $googleMapsKey = Setting::getValue('google_maps_api_key', '');
        $storeLat = Setting::getValue('store_latitude', '-7.7609582');
        $storeLng = Setting::getValue('store_longitude', '110.2529556');
        $deliveryFeeEnabled = Setting::getValue('delivery_fee_enabled', 'true') === 'true';
        $deliveryFeeAmount = (int) Setting::getValue('delivery_fee_amount', 10000);
        $discountEnabled = Setting::getValue('discount_enabled', 'true') === 'true';
        $discountPercentage = (int) Setting::getValue('discount_percentage', 10);

        return view('order.create', compact('products', 'categories', 'storeWhatsapp', 'canOrder', 'googleMapsKey', 'storeLat', 'storeLng', 'deliveryFeeEnabled', 'deliveryFeeAmount', 'discountEnabled', 'discountPercentage'));
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
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:200',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $customer = Customer::where('phone', $validated['phone'])->first();
        $wasMember = $customer ? $customer->is_member : false;

        if ($customer) {
            $customer->update([
                'name' => $validated['name'],
                'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : ($customer->address ?? null),
                'is_member' => $customer->is_member || $request->has('is_member') || auth()->check(),
                'user_id' => auth()->check() ? auth()->id() : ($customer->user_id ?? null),
            ]);
        } else {
            $customer = Customer::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : null,
                'is_member' => $request->has('is_member') || auth()->check(),
                'user_id' => auth()->check() ? auth()->id() : null,
            ]);
        }

        if (!$wasMember && $customer->is_member) {
            session()->flash('newly_joined_member', true);
        }

        $total = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $variantPrice = 0;

            // Handle variant selection
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::findOrFail($item['variant_id']);
                if (!$variant->is_available || $variant->stock <= 0) {
                    return back()->with('error', "Maaf, varian {$variant->name} untuk {$product->name} sudah habis.")->withInput();
                }
                if ($item['quantity'] > $variant->stock) {
                    return back()->with('error', "Maaf, stok varian {$variant->name} untuk {$product->name} hanya tersisa {$variant->stock} pcs.")->withInput();
                }
                $variantPrice = $variant->price_adjustment;
            }

            // Validate stock availability
            if ($product->stock <= 0) {
                return back()->with('error', "Maaf, {$product->name} sudah habis (stok kosong).")->withInput();
            }
            if ($item['quantity'] > $product->stock) {
                return back()->with('error', "Maaf, stok {$product->name} hanya tersisa {$product->stock} pcs.")->withInput();
            }

            $itemPrice = $product->price + $variantPrice;
            $subtotal = $itemPrice * $item['quantity'];
            $total += $subtotal;
            $orderItems[] = [
                'product_id' => $product->id,
                'variant_id' => !empty($item['variant_id']) ? $item['variant_id'] : null,
                'quantity' => $item['quantity'],
                'price' => $itemPrice,
                'subtotal' => $subtotal,
                'note' => !empty($item['note']) ? $item['note'] : null,
            ];
        }

        // Apply discount and shipping fee to get the actual total paid
        $deliveryFeeEnabled = Setting::getValue('delivery_fee_enabled', 'true') === 'true';
        $deliveryFeeAmount = (int) Setting::getValue('delivery_fee_amount', 10000);
        $discountEnabled = Setting::getValue('discount_enabled', 'true') === 'true';
        $discountPercentage = (int) Setting::getValue('discount_percentage', 10);

        if ($discountEnabled) {
            $discount = round($total * $discountPercentage / 100);
            $total -= $discount;
        }

        if ($validated['type'] === 'delivery' && $deliveryFeeEnabled) {
            $total += $deliveryFeeAmount;
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'order_date' => now(),
            'pickup_date' => $validated['pickup_date'],
            'type' => $validated['type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'total' => $total,
            'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'google_maps_link' => ($validated['latitude'] ?? null) && ($validated['longitude'] ?? null)
                ? "https://www.google.com/maps?q={$validated['latitude']},{$validated['longitude']}"
                : null,
        ]);

        // Member points accumulation & level up detection
        if ($customer->is_member) {
            $pointsEarned = (int) floor($total / 10000);
            if ($pointsEarned > 0) {
                $oldRank = $customer->rank_name;
                $customer->increment('points', $pointsEarned);
                $newRank = $customer->fresh()->rank_name;

                session()->flash('points_earned', $pointsEarned);

                if ($newRank !== $oldRank) {
                    session()->flash('level_up', [
                        'old' => $oldRank,
                        'new' => $newRank,
                        'badge' => $customer->fresh()->rank_badge
                    ]);
                }
            }
        }

        foreach ($orderItems as $item) {
            $order->items()->create($item);

            // Decrement product stock
            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);

            // Decrement variant stock if applicable
            if (!empty($item['variant_id'])) {
                ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
            }
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
        if ($order->customer->is_member) {
            $message .= "Status Member: Ya (Daftar Baru)\n";
        }
        $message .= "Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\n";

        foreach ($order->items as $item) {
            $itemName = $item->product->name;
            if ($item->variant) {
                $itemName .= ' (' . $item->variant->name . ')';
            }
            $message .= "- {$itemName} x {$item->quantity}";
            if ($item->note) {
                $message .= " (Catatan: {$item->note})";
            }
            $message .= "\n";
        }

        $message .= "\nTerima kasih.";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    public function history()
    {
        $userId = auth()->id();
        
        $customerIds = Customer::where('user_id', $userId)->pluck('id');
        
        $orders = Order::with(['items.product', 'customer'])
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->get();
            
        $storeWhatsapp = Setting::getValue('store_whatsapp');
            
        return view('order.history', compact('orders', 'storeWhatsapp'));
    }
}
