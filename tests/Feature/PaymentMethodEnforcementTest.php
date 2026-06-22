<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::setValue('store_whatsapp', '6281234567890');

        // Create a user and a member customer
        $this->user = User::factory()->create([
            'name' => 'Budi Member'
        ]);
        $this->customer = Customer::create([
            'name' => 'Budi Member',
            'phone' => '08111111111',
            'address' => 'Yogyakarta',
            'is_member' => true,
            'user_id' => $this->user->id,
            'points' => 10,
        ]);
    }

    public function test_payment_method_account_name_enforces_registered_name_on_store()
    {
        $this->actingAs($this->user);

        $payload = [
            'type' => 'e_wallet',
            'provider' => 'DANA',
            'account_number' => '08111111111',
            'account_name' => 'Ahmad Budi', // Try sending a different account name
        ];

        $response = $this->post(route('member.payment-method.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert payment method is saved but the account_name matches the customer's registered name
        $pm = PaymentMethod::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($pm);
        $this->assertEquals('Budi Member', $pm->account_name);
    }

    public function test_payment_method_account_name_enforces_registered_name_on_update()
    {
        $this->actingAs($this->user);

        // Create a payment method first
        $pm = $this->customer->paymentMethods()->create([
            'type' => 'bank_transfer',
            'provider' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Budi Member',
            'is_default' => true,
        ]);

        $payload = [
            'type' => 'bank_transfer',
            'provider' => 'Mandiri',
            'account_number' => '0987654321',
            'account_name' => 'Ahmad Budi', // Try updating to a different name
        ];

        $response = $this->put(route('member.payment-method.update', $pm->id), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert payment method is updated but the account_name still matches the customer's registered name
        $pm->refresh();
        $this->assertEquals('Mandiri', $pm->provider);
        $this->assertEquals('0987654321', $pm->account_number);
        $this->assertEquals('Budi Member', $pm->account_name);
    }

    public function test_payment_methods_sync_when_customer_registered_name_is_updated()
    {
        $this->actingAs($this->user);

        // Create two payment methods
        $pm1 = $this->customer->paymentMethods()->create([
            'type' => 'e_wallet',
            'provider' => 'DANA',
            'account_number' => '08111111111',
            'account_name' => 'Budi Member',
            'is_default' => true,
        ]);

        $pm2 = $this->customer->paymentMethods()->create([
            'type' => 'bank_transfer',
            'provider' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Budi Member',
            'is_default' => false,
        ]);

        // Update customer registered name via profile update route
        $payload = [
            'name' => 'Budi Member Updated',
            'phone' => '08111111111',
            'address' => 'New Yogyakarta Address',
        ];

        $response = $this->post(route('member.profile.update'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert customer registered name is updated
        $this->customer->refresh();
        $this->assertEquals('Budi Member Updated', $this->customer->name);

        // Assert all associated payment methods are updated automatically
        $pm1->refresh();
        $pm2->refresh();

        $this->assertEquals('Budi Member Updated', $pm1->account_name);
        $this->assertEquals('Budi Member Updated', $pm2->account_name);
    }
}
