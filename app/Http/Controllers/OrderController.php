<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

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
            'payment_method' => 'nullable|string',
        ]);

        $customer = null;
        if (auth()->check()) {
            $customer = auth()->user()->customer;
        }

        $guestCustomer = Customer::where('phone', $validated['phone'])->first();

        // If the logged-in user already has a customer record AND there is an existing guest customer record with the same phone
        if ($customer && $guestCustomer && $customer->id !== $guestCustomer->id) {
            if (is_null($guestCustomer->user_id)) {
                // Merge guest customer's points and orders into the logged-in customer's profile
                $customer->update([
                    'points' => max($customer->points, $guestCustomer->points),
                ]);
                
                // Re-link orders
                \App\Models\Order::where('customer_id', $guestCustomer->id)->update(['customer_id' => $customer->id]);
                
                // Delete guest customer
                $guestCustomer->delete();
            }
        }

        if (!$customer) {
            $customer = $guestCustomer; // reuse the guest customer if found
        }

        $wasMember = $customer ? $customer->is_member : false;
        $isLoggedIn = auth()->check();

        if ($customer) {
            $customer->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : ($customer->address ?? null),
                'is_member' => $customer->is_member || $isLoggedIn,
                'user_id' => $isLoggedIn ? auth()->id() : ($customer->user_id ?? null),
            ]);
        } else {
            $customer = Customer::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : null,
                'is_member' => $isLoggedIn,
                'user_id' => $isLoggedIn ? auth()->id() : null,
            ]);
        }

        if ($isLoggedIn && !$wasMember && $customer->is_member) {
            session()->flash('newly_joined_member', true);
        }

        if (!$isLoggedIn && $request->has('is_member')) {
            session()->flash('wants_to_join_member', true);
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

        $paymentMethodName = 'Transfer Bank / QRIS';
        $paymentMethodVal = $validated['payment_method'] ?? 'transfer';
        $savedPm = null;
        if ($paymentMethodVal === 'transfer') {
            $paymentMethodName = 'Transfer Bank / QRIS';
        } elseif ($paymentMethodVal === 'midtrans') {
            $paymentMethodName = 'Online Payment / Midtrans';
        } elseif ($paymentMethodVal === 'whatsapp') {
            $paymentMethodName = 'WhatsApp Confirmation';
        } elseif ($paymentMethodVal === 'cod') {
            $paymentMethodName = 'Cash On Delivery / COD';
        } elseif (str_starts_with($paymentMethodVal, 'saved_')) {
            $pmId = (int) str_replace('saved_', '', $paymentMethodVal);
            if (auth()->check() && auth()->user()->customer) {
                $savedPm = auth()->user()->customer->paymentMethods()->find($pmId);
                if ($savedPm) {
                    $paymentMethodName = "{$savedPm->provider} - {$savedPm->account_name} ({$savedPm->account_number})";
                }
            }
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'order_date' => now(),
            'pickup_date' => $validated['pickup_date'],
            'type' => $validated['type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $paymentMethodName,
            'total' => $total,
            'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? null) : null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'google_maps_link' => ($validated['latitude'] ?? null) && ($validated['longitude'] ?? null)
                ? "https://www.google.com/maps?q={$validated['latitude']},{$validated['longitude']}"
                : null,
        ]);


        foreach ($orderItems as $item) {
            $order->items()->create($item);

            // Decrement product stock
            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);

            // Decrement variant stock if applicable
            if (!empty($item['variant_id'])) {
                ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
            }
        }

        // Generate Midtrans Snap Token if payment method is midtrans or a saved payment method
        if ($paymentMethodVal === 'midtrans' || $savedPm !== null) {
            try {
                $this->initMidtrans();
                
                $midtransItems = [];
                foreach ($orderItems as $item) {
                    $product = Product::find($item['product_id']);
                    $variant = !empty($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
                    
                    $name = $product->name;
                    if ($variant) {
                        $name .= ' (' . $variant->name . ')';
                    }
                    
                    if (strlen($name) > 50) {
                        $name = substr($name, 0, 47) . '...';
                    }
                    
                    $midtransItems[] = [
                        'id' => 'prod-' . $item['product_id'] . ($item['variant_id'] ? '-v' . $item['variant_id'] : ''),
                        'price' => (int) $item['price'],
                        'quantity' => (int) $item['quantity'],
                        'name' => $name,
                    ];
                }
                
                if ($validated['type'] === 'delivery' && $deliveryFeeEnabled) {
                    $midtransItems[] = [
                        'id' => 'delivery-fee',
                        'price' => (int) $deliveryFeeAmount,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim / Delivery Fee',
                    ];
                }
                
                if ($discountEnabled) {
                    $subtotalAmount = 0;
                    foreach ($orderItems as $item) {
                        $subtotalAmount += $item['price'] * $item['quantity'];
                    }
                    $discountAmt = round($subtotalAmount * $discountPercentage / 100);
                    if ($discountAmt > 0) {
                        $midtransItems[] = [
                            'id' => 'discount',
                            'price' => -((int) $discountAmt),
                            'quantity' => 1,
                            'name' => 'Diskon Promo (' . $discountPercentage . '%)',
                        ];
                    }
                }

                // Setup pre-selected enabled payment channels based on registered type
                $enabledPayments = [];
                if ($savedPm) {
                    if ($savedPm->type === 'credit_card') {
                        $enabledPayments = ['credit_card'];
                    } elseif ($savedPm->type === 'e_wallet') {
                        $enabledPayments = ['gopay', 'shopeepay'];
                    } elseif ($savedPm->type === 'bank_transfer') {
                        // Limit to supported virtual accounts
                        $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va'];
                    }
                }

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number . '-' . $order->created_at->timestamp,
                        'gross_amount' => (int) $total,
                    ],
                    'item_details' => $midtransItems,
                    'customer_details' => [
                        'first_name' => $validated['name'],
                        'phone' => $validated['phone'],
                        'billing_address' => [
                            'first_name' => $validated['name'],
                            'phone' => $validated['phone'],
                            'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? '') : 'Ambil di Toko',
                        ],
                        'shipping_address' => [
                            'first_name' => $validated['name'],
                            'phone' => $validated['phone'],
                            'address' => $validated['type'] === 'delivery' ? ($validated['address'] ?? '') : 'Ambil di Toko',
                        ],
                    ],
                ];

                if (!empty($enabledPayments)) {
                    $params['enabled_payments'] = $enabledPayments;
                }

                // If utilizing credit card, configure card tokenization settings
                if ($savedPm && $savedPm->type === 'credit_card') {
                    $params['credit_card'] = [
                        'secure' => true,
                        'save_card' => true
                    ];
                    $params['user_id'] = 'customer-' . $order->customer_id;
                }

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Error for order ' . $order->order_number . ': ' . $e->getMessage());
            }
        }

        // Create kitchen task immediately for non-Midtrans and non-transfer orders.
        // Midtrans and Transfer Bank / QRIS orders will only create kitchen task when payment succeeds/is confirmed.
        if ($paymentMethodVal !== 'midtrans' && $paymentMethodVal !== 'transfer' && $savedPm === null) {
            $order->kitchenTask()->create([
                'status' => 'pending',
            ]);
        }

        $whatsappUrl = $this->generateWhatsappUrl($order);

        return redirect()->route('order.success', $order->id)
            ->with('whatsapp_url', $whatsappUrl);
    }

    public function success($id)
    {
        $order = Order::with(['customer', 'items.product', 'items.variant'])->findOrFail($id);
        
        // Auto-check payment status from Midtrans on success page load (acts as a webhook fallback)
        if ($order->payment_status === 'unpaid' && (stripos($order->payment_method, 'midtrans') !== false)) {
            try {
                $this->initMidtrans();
                $midtransOrderId = $order->order_number . '-' . $order->created_at->timestamp;
                $status = \Midtrans\Transaction::status($midtransOrderId);
                
                $transactionStatus = $status->transaction_status;
                $fraudStatus = $status->fraud_status;
                
                $isPaid = false;
                if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                    $isPaid = true;
                }
                
                if ($isPaid) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);
                    
                    if (!$order->kitchenTask) {
                        $order->kitchenTask()->create([
                            'status' => 'pending',
                        ]);
                    }
                    
                    // Reload fresh order to reflect changes in the view
                    $order = $order->fresh(['customer', 'items.product', 'items.variant']);
                }
            } catch (\Exception $e) {
                \Log::info('Midtrans auto-check failed or transaction not created yet: ' . $e->getMessage());
            }
        }

        // Regenerate snap token on-the-fly if it is missing for Midtrans payments
        if (!$order->snap_token && $order->payment_status === 'unpaid' && (stripos($order->payment_method, 'midtrans') !== false)) {
            try {
                $this->initMidtrans();
                
                $midtransItems = [];
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $variant = $item->variant;
                    
                    $name = $product->name;
                    if ($variant) {
                        $name .= ' (' . $variant->name . ')';
                    }
                    
                    if (strlen($name) > 50) {
                        $name = substr($name, 0, 47) . '...';
                    }
                    
                    $midtransItems[] = [
                        'id' => 'prod-' . $item->product_id . ($item->variant_id ? '-v' . $item->variant_id : ''),
                        'price' => (int) $item->price,
                        'quantity' => (int) $item->quantity,
                        'name' => $name,
                    ];
                }
                
                $deliveryFeeEnabled = Setting::getValue('delivery_fee_enabled', 'true') === 'true';
                $deliveryFeeAmount = (int) Setting::getValue('delivery_fee_amount', 10000);
                if ($order->type === 'delivery' && $deliveryFeeEnabled) {
                    $midtransItems[] = [
                        'id' => 'delivery-fee',
                        'price' => (int) $deliveryFeeAmount,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim / Delivery Fee',
                    ];
                }
                
                $discountEnabled = Setting::getValue('discount_enabled', 'true') === 'true';
                $discountPercentage = (int) Setting::getValue('discount_percentage', 10);
                if ($discountEnabled) {
                    $subtotalAmount = 0;
                    foreach ($order->items as $item) {
                        $subtotalAmount += $item->price * $item->quantity;
                    }
                    $discountAmt = round($subtotalAmount * $discountPercentage / 100);
                    if ($discountAmt > 0) {
                        $midtransItems[] = [
                            'id' => 'discount',
                            'price' => -((int) $discountAmt),
                            'quantity' => 1,
                            'name' => 'Diskon Promo (' . $discountPercentage . '%)',
                        ];
                    }
                }

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number . '-' . $order->created_at->timestamp,
                        'gross_amount' => (int) $order->total,
                    ],
                    'item_details' => $midtransItems,
                    'customer_details' => [
                        'first_name' => $order->customer->name,
                        'phone' => $order->customer->phone,
                        'billing_address' => [
                            'first_name' => $order->customer->name,
                            'phone' => $order->customer->phone,
                            'address' => $order->type === 'delivery' ? ($order->address ?? '') : 'Ambil di Toko',
                        ],
                        'shipping_address' => [
                            'first_name' => $order->customer->name,
                            'phone' => $order->customer->phone,
                            'address' => $order->type === 'delivery' ? ($order->address ?? '') : 'Ambil di Toko',
                        ],
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Log::error('Midtrans Snap Regeneration Error for order ' . $order->order_number . ': ' . $e->getMessage());
            }
        }

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
        $message .= "Total: Rp " . number_format($order->total, 0, ',', '.') . "\n";
        if ($order->payment_method) {
            $message .= "Metode Pembayaran: {$order->payment_method}\n";
        }
        $message .= "\n";

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

    public function statusJson($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        $maxReadyTime = '15-20 min';
        if ($order->items->isNotEmpty()) {
            $maxMinutes = 0;
            $maxTimeStr = '15-20 min';
            foreach ($order->items as $item) {
                $timeStr = $item->product->ready_time ?? '15-20 min';
                preg_match_all('/\d+/', $timeStr, $matches);
                if (!empty($matches[0])) {
                    $mins = max(array_map('intval', $matches[0]));
                    if (stripos($timeStr, 'jam') !== false || stripos($timeStr, 'hour') !== false || stripos($timeStr, 'hr') !== false) {
                        $mins *= 60;
                    }
                    if ($mins > $maxMinutes) {
                        $maxMinutes = $mins;
                        $maxTimeStr = $timeStr;
                    }
                }
            }
            $maxReadyTime = $maxTimeStr;
        }

        $bakingDuration = (int) Setting::getValue('baking_duration_minutes', 15);
        $deliveryDuration = (int) Setting::getValue('delivery_duration_minutes', 20);

        return response()->json([
            'status' => $order->status,
            'type' => $order->type,
            'max_ready_time' => $maxReadyTime,
            'updated_at' => $order->updated_at->toIso8601String(),
            'baking_duration_minutes' => $bakingDuration,
            'delivery_duration_minutes' => $deliveryDuration,
            'current_time' => now()->toIso8601String()
        ]);
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

    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function handleNotification(Request $request)
    {
        try {
            $this->initMidtrans();
            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $orderIdWithTimestamp = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            // Extract the actual order number from order_id (e.g. MTH-20260623-001-171829392 => MTH-20260623-001)
            $parts = explode('-', $orderIdWithTimestamp);
            if (count($parts) >= 3) {
                $orderNumber = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            } else {
                $orderNumber = $orderIdWithTimestamp;
            }

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update([
                        'payment_status' => 'unpaid',
                        'status' => 'pending'
                    ]);
                } else if ($fraudStatus == 'accept') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);
                    if (!$order->kitchenTask) {
                        $order->kitchenTask()->create(['status' => 'pending']);
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
                if (!$order->kitchenTask) {
                    $order->kitchenTask()->create(['status' => 'pending']);
                }
            } else if ($transactionStatus == 'pending') {
                $order->update([
                    'payment_status' => 'unpaid'
                ]);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                if ($order->status !== 'cancelled') {
                    $order->update([
                        'payment_status' => 'unpaid',
                        'status' => 'cancelled'
                    ]);

                    // Restore stock
                    foreach ($order->items as $item) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                        if ($item->variant_id) {
                            ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Webhook handled successfully']);
        } catch (\Exception $e) {
            \Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Webhook error', 'error' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        try {
            $this->initMidtrans();
            
            // Query status directly from Midtrans API to verify payment status
            $status = \Midtrans\Transaction::status($request->order_id);
            
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status;
            
            // Extract actual order number from order_id
            $parts = explode('-', $request->order_id);
            if (count($parts) >= 3) {
                $orderNumber = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            } else {
                $orderNumber = $request->order_id;
            }
            
            $order = Order::where('order_number', $orderNumber)->first();
            
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }
            
            $isPaid = false;
            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                $isPaid = true;
            }
            
            if ($isPaid) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
                
                // Automatically deliver to kitchen
                if (!$order->kitchenTask) {
                    $order->kitchenTask()->create([
                        'status' => 'pending',
                    ]);
                }
                
                return response()->json(['success' => true, 'message' => 'Payment confirmed and sent to kitchen.']);
            }
            
            return response()->json(['success' => false, 'message' => 'Payment status is: ' . $transactionStatus]);
        } catch (\Exception $e) {
            \Log::error('Payment confirmation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
