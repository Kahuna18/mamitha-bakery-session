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
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}

