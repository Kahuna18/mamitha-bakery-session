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
}
