<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTabsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '081234567890',
            'address' => 'Test Address',
        ]);
    }

    public function test_incoming_tab_shows_paid_or_cod_wa_orders()
    {
        // 1. Unpaid midtrans order (should NOT show in incoming tab)
        $unpaidMidtrans = Order::create([
            'order_number' => 'MTH-001',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Online Payment / Midtrans',
            'payment_status' => 'unpaid',
            'total' => 10000,
        ]);

        // 2. Paid midtrans order (should show in incoming tab)
        $paidMidtrans = Order::create([
            'order_number' => 'MTH-002',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Online Payment / Midtrans',
            'payment_status' => 'paid',
            'total' => 10000,
        ]);

        // 3. Unpaid COD order (should show in incoming tab because it is COD)
        $codOrder = Order::create([
            'order_number' => 'MTH-003',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Cash On Delivery / COD',
            'payment_status' => 'unpaid',
            'total' => 10000,
        ]);

        // 4. Completed order (should NOT show in incoming tab)
        $completedOrder = Order::create([
            'order_number' => 'MTH-004',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'done',
            'payment_method' => 'Cash On Delivery / COD',
            'payment_status' => 'paid',
            'total' => 10000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['tab' => 'incoming']));
        
        $response->assertStatus(200);
        $response->assertSee('MTH-002');
        $response->assertSee('MTH-003');
        $response->assertDontSee('MTH-001');
        $response->assertDontSee('MTH-004');
    }

    public function test_pending_payment_tab_shows_unpaid_prepaid_orders()
    {
        // 1. Unpaid midtrans order (should show in pending_payment tab)
        $unpaidMidtrans = Order::create([
            'order_number' => 'MTH-001',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Online Payment / Midtrans',
            'payment_status' => 'unpaid',
            'total' => 10000,
        ]);

        // 2. Paid midtrans order (should NOT show in pending_payment tab)
        $paidMidtrans = Order::create([
            'order_number' => 'MTH-002',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Online Payment / Midtrans',
            'payment_status' => 'paid',
            'total' => 10000,
        ]);

        // 3. Unpaid COD order (should NOT show in pending_payment tab because COD is active immediately)
        $codOrder = Order::create([
            'order_number' => 'MTH-003',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Cash On Delivery / COD',
            'payment_status' => 'unpaid',
            'total' => 10000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['tab' => 'pending_payment']));
        
        $response->assertStatus(200);
        $response->assertSee('MTH-001');
        $response->assertDontSee('MTH-002');
        $response->assertDontSee('MTH-003');
    }

    public function test_kitchen_tab_shows_producing_or_ready_orders()
    {
        $producingOrder = Order::create([
            'order_number' => 'MTH-001',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'producing',
            'payment_method' => 'Cash On Delivery / COD',
            'payment_status' => 'paid',
            'total' => 10000,
        ]);

        $confirmedOrder = Order::create([
            'order_number' => 'MTH-002',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'confirmed',
            'payment_method' => 'Cash On Delivery / COD',
            'payment_status' => 'paid',
            'total' => 10000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['tab' => 'kitchen']));
        
        $response->assertStatus(200);
        $response->assertSee('MTH-001');
        $response->assertDontSee('MTH-002');
    }

    public function test_check_new_orders_endpoint_polling_behavior()
    {
        // Setup initial poll time
        $pollTime = now()->toIso8601String();

        // 1. Create unpaid midtrans order (should NOT trigger alarm on checkNewOrders)
        $unpaidMidtrans = Order::create([
            'order_number' => 'MTH-001',
            'customer_id' => $this->customer->id,
            'order_date' => now(),
            'pickup_date' => now()->addDay(),
            'type' => 'pickup',
            'status' => 'pending',
            'payment_method' => 'Online Payment / Midtrans',
            'payment_status' => 'unpaid',
            'total' => 10000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.check-new-orders', [
            'last_poll_time' => $pollTime,
        ]));
        
        $response->assertStatus(200);
        $response->assertJson([
            'new_orders_count' => 0,
        ]);

        // 2. Mark that order as paid (simulating webhook payment completion)
        // Manually set updated_at to be in the future relative to $pollTime
        $unpaidMidtrans->updated_at = now()->addMinutes(5);
        $unpaidMidtrans->payment_status = 'paid';
        $unpaidMidtrans->status = 'confirmed';
        $unpaidMidtrans->save();

        $response2 = $this->actingAs($this->admin)->get(route('admin.check-new-orders', [
            'last_poll_time' => $pollTime,
        ]));
        
        $response2->assertStatus(200);
        $response2->assertJson([
            'new_orders_count' => 1,
        ]);
    }
}
