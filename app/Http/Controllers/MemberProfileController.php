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
            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }
        
        $user->update([
            'name' => $request->name,
        ]);
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}

