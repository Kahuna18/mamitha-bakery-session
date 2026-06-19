<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberRankTest extends TestCase
{
    use RefreshDatabase;

    protected $category;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::setValue('is_closed', 'false');
        Setting::setValue('daily_order_limit', '0');
        Setting::setValue('store_whatsapp', '6281234567890');
        Setting::setValue('discount_enabled', 'false');
        Setting::setValue('delivery_fee_enabled', 'false');

        // Create basic category & product for ordering
        $this->category = Category::create([
            'name' => 'Roti',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Croissant Coklat',
            'price' => 15000,
            'is_available' => true,
            'stock' => 50,
        ]);
    }

    public function test_guest_can_order_normally_without_member_features()
    {
        $payload = [
            'name' => 'Budi Guest',
            'phone' => '08999999999',
            'address' => 'Sleman, Jogja',
            'type' => 'pickup',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'notes' => 'Tolong dibungkus rapi',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2, // 30.000 total
                ]
            ],
        ];

        $response = $this->post(route('order.store'), $payload);

        // Assert redirect to success page
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('whatsapp_url');
        $response->assertSessionMissing('points_earned');
        $response->assertSessionMissing('level_up');

        // Assert customer is created, but is not a member and has 0 points
        $customer = Customer::where('phone', '08999999999')->first();
        $this->assertNotNull($customer);
        $this->assertFalse((bool)$customer->is_member);
        $this->assertEquals(0, $customer->points);
    }

    public function test_member_earns_points_on_order()
    {
        // Create user and member customer
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Budi Member',
            'phone' => '08111111111',
            'address' => 'Yogyakarta',
            'is_member' => true,
            'user_id' => $user->id,
            'points' => 10, // starts at 10 points (Bronze)
        ]);

        $this->actingAs($user);

        $payload = [
            'name' => 'Budi Member',
            'phone' => '08111111111',
            'address' => 'Yogyakarta',
            'type' => 'pickup',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2, // 30.000 total -> 3 points
                ]
            ],
        ];

        $response = $this->post(route('order.store'), $payload);

        // Assert redirect to success page and points earned flashed to session
        $response->assertRedirect();
        $response->assertSessionHas('points_earned', 3);
        $response->assertSessionMissing('level_up'); // No level up yet (13 points total is still Bronze)

        // Assert points updated in database
        $customer->refresh();
        $this->assertEquals(13, $customer->points);
        $this->assertEquals('Bronze', $customer->rank_name);
    }

    public function test_member_triggers_level_up_on_rank_promotion()
    {
        // Create user and member customer near silver promotion threshold
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Budi Member Pro',
            'phone' => '08222222222',
            'address' => 'Yogyakarta',
            'is_member' => true,
            'user_id' => $user->id,
            'points' => 95, // 95 points (Bronze, needs 5 points to hit 100 for Silver)
        ]);

        $this->actingAs($user);

        $payload = [
            'name' => 'Budi Member Pro',
            'phone' => '08222222222',
            'address' => 'Yogyakarta',
            'type' => 'pickup',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 4, // 60.000 total -> 6 points -> total 101 points (Silver)
                ]
            ],
        ];

        $response = $this->post(route('order.store'), $payload);

        // Assert redirect, points earned and level_up flashed
        $response->assertRedirect();
        $response->assertSessionHas('points_earned', 6);
        $response->assertSessionHas('level_up', [
            'old' => 'Bronze',
            'new' => 'Silver',
            'badge' => '🥈',
        ]);

        // Assert database points and rank
        $customer->refresh();
        $this->assertEquals(101, $customer->points);
        $this->assertEquals('Silver', $customer->rank_name);
    }

    public function test_profile_merges_when_updating_phone_to_existing_guest()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Guest customer record with points and orders
        $guestCustomer = Customer::create([
            'name' => 'Guest User',
            'phone' => '08123456789',
            'points' => 50,
            'is_member' => false,
        ]);

        // Temporary customer record generated when user registers and goes to profile index
        $tempCustomer = Customer::create([
            'name' => $user->name,
            'phone' => '',
            'is_member' => true,
            'user_id' => $user->id,
            'points' => 0,
        ]);

        // Update profile phone to the guest's phone number
        $response = $this->post(route('member.profile.update'), [
            'name' => 'Merged User',
            'phone' => '08123456789',
            'address' => 'New Address',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert that the temporary empty profile was deleted
        $this->assertDatabaseMissing('customers', [
            'id' => $tempCustomer->id,
        ]);

        // Assert that the guest customer profile is now linked to user and has merged data
        $guestCustomer->refresh();
        $this->assertEquals($user->id, $guestCustomer->user_id);
        $this->assertTrue((bool)$guestCustomer->is_member);
        $this->assertEquals(50, $guestCustomer->points);
        $this->assertEquals('Merged User', $guestCustomer->name);
        $this->assertEquals('New Address', $guestCustomer->address);
    }

    public function test_admin_can_edit_member_points()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $member = Customer::create([
            'name' => 'Test Member',
            'phone' => '08123456780',
            'is_member' => true,
            'points' => 10,
        ]);

        $response = $this->post(route('admin.update-customer-points', $member), [
            'points' => 350,
        ]);

        $response->assertRedirect();
        
        $member->refresh();
        $this->assertEquals(350, $member->points);
        $this->assertEquals('Gold', $member->rank_name);
    }

    public function test_admin_can_reset_member_points()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $member = Customer::create([
            'name' => 'Test Member To Reset',
            'phone' => '08123456781',
            'is_member' => true,
            'points' => 150,
        ]);

        $response = $this->post(route('admin.reset-member-points', $member));

        $response->assertRedirect();
        
        $member->refresh();
        $this->assertEquals(0, $member->points);
        $this->assertEquals('Bronze', $member->rank_name);
    }

    public function test_admin_can_delete_member_without_orders()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $member = Customer::create([
            'name' => 'Test Member To Delete',
            'phone' => '08123456782',
            'is_member' => true,
            'points' => 10,
        ]);

        $response = $this->delete(route('admin.delete-member', $member));

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('customers', [
            'id' => $member->id,
        ]);
    }

    public function test_admin_deletes_member_with_orders_gracefully_deactivates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $member = Customer::create([
            'name' => 'Test Member With Orders',
            'phone' => '08123456783',
            'is_member' => true,
            'points' => 200,
        ]);

        // Create an order for this customer
        \App\Models\Order::create([
            'customer_id' => $member->id,
            'order_number' => 'ORD-12345',
            'status' => 'pending',
            'total' => 50000,
            'pickup_date' => now()->addDay()->toDateString(),
            'type' => 'pickup',
            'order_date' => now()->toDateString(),
        ]);

        $response = $this->delete(route('admin.delete-member', $member));

        $response->assertRedirect();
        
        // Assert customer was NOT deleted
        $this->assertDatabaseHas('customers', [
            'id' => $member->id,
        ]);

        // Assert member status is disabled and points reset to 0
        $member->refresh();
        $this->assertFalse((bool)$member->is_member);
        $this->assertEquals(0, $member->points);
    }

    public function test_admin_can_reset_all_members_and_auto_increment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $user1 = User::create([
            'name' => 'Member One',
            'email' => 'member1@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $member1 = Customer::create([
            'name' => 'Member One',
            'phone' => '08123456784',
            'is_member' => true,
            'points' => 50,
            'user_id' => $user1->id,
        ]);

        $user2 = User::create([
            'name' => 'Member Two',
            'email' => 'member2@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $member2 = Customer::create([
            'name' => 'Member Two',
            'phone' => '08123456785',
            'is_member' => true,
            'points' => 120,
            'user_id' => $user2->id,
        ]);

        // Send reset request
        $response = $this->post(route('admin.customers.reset-members'));

        $response->assertRedirect(route('admin.dashboard', ['tab' => 'member']));
        $response->assertSessionHas('success');

        // Check customers database is empty
        $this->assertEquals(0, Customer::count());

        // Check customer users are deleted
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
        
        // Assert admin user is still present
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // Create new customer and check if auto-increment is reset (should start from 1)
        $newMember = Customer::create([
            'name' => 'New Member After Reset',
            'phone' => '08123456786',
            'is_member' => true,
            'points' => 0,
        ]);

        $this->assertEquals(1, $newMember->id);
    }
}
