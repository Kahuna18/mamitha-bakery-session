<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class MemberProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Find or auto-initialize customer profile for logged-in member
        $customer = $user->customer;
        
        if (!$customer) {
            $customer = Customer::create([
                'name' => $user->name,
                'phone' => '',
                'address' => '',
                'is_member' => true,
                'user_id' => $user->id,
                'points' => 0
            ]);
        }

        // Fetch member's order history
        $orders = Order::with(['items.product', 'customer'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        $storeWhatsapp = Setting::getValue('store_whatsapp');

        $customer->load('paymentMethods');

        return view('member.profile', compact('customer', 'orders', 'storeWhatsapp'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        if ($customer) {
            // Check if there is an existing guest customer with this phone number
            $existingCustomer = Customer::where('phone', $request->phone)
                ->where('id', '!=', $customer->id)
                ->first();
                
            if ($existingCustomer) {
                // If it is a guest (user_id is null) or linked to the same user
                if (is_null($existingCustomer->user_id) || $existingCustomer->user_id == $user->id) {
                    // Re-link any orders of the temporary customer to the existing one
                    \App\Models\Order::where('customer_id', $customer->id)->update(['customer_id' => $existingCustomer->id]);
                    
                    // Merge points (take the max of both)
                    $mergedPoints = max($customer->points, $existingCustomer->points);
                    
                    // Delete the temporary customer profile
                    $customer->delete();
                    
                    // Update the existing customer record with the user ID, name, phone, address, and merged points
                    $existingCustomer->update([
                        'user_id' => $user->id,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address ?: $existingCustomer->address,
                        'points' => $mergedPoints,
                        'is_member' => true,
                    ]);
                } else {
                    // If the existing customer is linked to another user, just update the current customer
                    $customer->update([
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                    ]);
                }
            } else {
                // No existing customer with this phone, just update normally
                $customer->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            }
        } else {
            // If the customer profile doesn't exist at all yet, check if there's an existing guest customer
            $existingCustomer = Customer::where('phone', $request->phone)->first();
            if ($existingCustomer && (is_null($existingCustomer->user_id) || $existingCustomer->user_id == $user->id)) {
                $existingCustomer->update([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address ?: $existingCustomer->address,
                ]);
            } else {
                Customer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'is_member' => true,
                    'user_id' => $user->id,
                    'points' => 0
                ]);
            }
        }
        
        $user->update([
            'name' => $request->name,
        ]);
        
        // Ensure all customer payment methods match the new registered name
        $finalCustomer = $user->fresh()->customer;
        if ($finalCustomer) {
            $finalCustomer->paymentMethods()->update(['account_name' => $request->name]);
        }
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function storePaymentMethod(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        if (!$customer) {
            return back()->withErrors(['message' => 'Profil pelanggan tidak ditemukan.']);
        }
        
        $request->validate([
            'type' => 'required|string|in:credit_card,e_wallet,bank_transfer',
            'provider' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);
        
        // Check if this is the first payment method, make it default
        $isFirst = $customer->paymentMethods()->count() === 0;
        
        $customer->paymentMethods()->create([
            'type' => $request->type,
            'provider' => $request->provider,
            'account_number' => $request->account_number,
            'account_name' => $customer->name, // Enforce registered name matching
            'is_default' => $isFirst,
        ]);
        
        return back()->with('success', 'Metode pembayaran berhasil ditambahkan!');
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        if (!$customer) {
            return back()->withErrors(['message' => 'Profil pelanggan tidak ditemukan.']);
        }
        
        $paymentMethod = $customer->paymentMethods()->findOrFail($id);
        
        $request->validate([
            'type' => 'required|string|in:credit_card,e_wallet,bank_transfer',
            'provider' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);
        
        $paymentMethod->update([
            'type' => $request->type,
            'provider' => $request->provider,
            'account_number' => $request->account_number,
            'account_name' => $customer->name, // Enforce registered name matching
        ]);
        
        return back()->with('success', 'Metode pembayaran berhasil diperbarui!');
    }

    public function destroyPaymentMethod($id)
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        if (!$customer) {
            return back()->withErrors(['message' => 'Profil pelanggan tidak ditemukan.']);
        }
        
        $paymentMethod = $customer->paymentMethods()->findOrFail($id);
        
        $wasDefault = $paymentMethod->is_default;
        
        $paymentMethod->delete();
        
        // If the deleted method was default, set the first remaining method as default
        if ($wasDefault) {
            $nextDefault = $customer->paymentMethods()->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }
        
        return back()->with('success', 'Metode pembayaran berhasil dihapus!');
    }
}

