<?php

namespace Tests\Feature;

use App\Models\Deceased;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CamPayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_initiate_campay_payment(): void
    {
        config([
            'services.campay.use_demo' => true,
            'services.campay.simulation' => false,
            'services.campay.base_url' => 'https://demo.campay.net',
            'services.campay.token' => 'test-permanent-token',
            'services.campay.username' => null,
            'services.campay.password' => null,
        ]);

        Http::fake([
            'https://demo.campay.net/api/collect/' => Http::response([
                'reference' => '11111111-2222-3333-4444-555555555555',
                'ussd_code' => '*126#',
                'operator' => 'MTN',
            ], 200),
        ]);

        $user = User::factory()->create(['role' => 'staff']);
        $deceased = Deceased::create([
            'user_id' => $user->id,
            'full_name' => 'Test Person',
            'gender' => 'male',
            'date_of_death' => now()->toDateString(),
            'admission_date' => now()->toDateString(),
            'cause_of_death' => 'n/a',
        ]);

        $response = $this->actingAs($user)->post(route('payments.store'), [
            'deceased_id' => $deceased->id,
            'amount' => 5,
            'balance' => 0,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'mobile_money',
            'mobile_operator' => 'MTN',
            'phone_number' => '650000000',
        ]);

        $payment = Payment::first();

        $this->assertNotNull($payment);
        $response->assertRedirect(route('payments.processing', $payment));
        $this->assertSame('11111111-2222-3333-4444-555555555555', $payment->campay_reference);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('237650000000', $payment->phone_number);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://demo.campay.net/api/collect/'
                && $request->header('Authorization')[0] === 'Token test-permanent-token'
                && $request['amount'] === '25'
                && $request['from'] === '237650000000';
        });
    }

    public function test_demo_mode_always_collects_25_regardless_of_entered_amount(): void
    {
        config([
            'services.campay.use_demo' => true,
            'services.campay.simulation' => false,
            'services.campay.base_url' => 'https://demo.campay.net',
            'services.campay.token' => 'test-permanent-token',
        ]);

        Http::fake([
            'https://demo.campay.net/api/collect/' => Http::response([
                'reference' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
                'ussd_code' => '*126#',
                'operator' => 'MTN',
            ], 200),
        ]);

        $user = User::factory()->create(['role' => 'staff']);
        $deceased = Deceased::create([
            'user_id' => $user->id,
            'full_name' => 'Test Person',
            'gender' => 'male',
            'date_of_death' => now()->toDateString(),
            'admission_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('payments.store'), [
            'deceased_id' => $deceased->id,
            'amount' => 15000,
            'balance' => 0,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'mobile_money',
            'mobile_operator' => 'MTN',
            'phone_number' => '650000000',
        ]);

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $response->assertRedirect(route('payments.processing', $payment));
        $this->assertEquals(15000, (float) $payment->amount);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://demo.campay.net/api/collect/'
                && $request['amount'] === '25'
                && $request['from'] === '237650000000';
        });
    }

    public function test_status_check_marks_payment_successful(): void
    {
        config([
            'services.campay.use_demo' => true,
            'services.campay.simulation' => false,
            'services.campay.base_url' => 'https://demo.campay.net',
            'services.campay.token' => 'test-permanent-token',
        ]);

        Http::fake([
            'https://demo.campay.net/api/transaction/*' => Http::response([
                'reference' => '11111111-2222-3333-4444-555555555555',
                'status' => 'SUCCESSFUL',
                'operator_reference' => 'OP-123',
            ], 200),
        ]);

        $user = User::factory()->create(['role' => 'staff']);
        $payment = Payment::create([
            'user_id' => $user->id,
            'deceased_id' => Deceased::create([
                'user_id' => $user->id,
                'full_name' => 'Test Person',
                'gender' => 'male',
                'date_of_death' => now()->toDateString(),
                'admission_date' => now()->toDateString(),
            ])->id,
            'amount' => 5,
            'balance' => 0,
            'payment_date' => now()->toDateString(),
            'receipt_number' => 'REC-TEST-1',
            'payment_method' => 'mobile_money',
            'mobile_operator' => 'MTN',
            'phone_number' => '237650000000',
            'status' => 'pending',
            'confirmed' => false,
            'campay_reference' => '11111111-2222-3333-4444-555555555555',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('payments.check-status', $payment));

        $response->assertOk()
            ->assertJson(['status' => 'SUCCESSFUL']);

        $payment->refresh();
        $this->assertSame('successful', $payment->status);
        $this->assertTrue($payment->confirmed);
        $this->assertSame('OP-123', $payment->campay_operator_reference);
    }
}
