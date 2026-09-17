<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentStatusMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payments_status_column_accepts_failed_and_successful(): void
    {
        $this->assertTrue(Schema::hasColumn('payments', 'status'));

        // Ensure the expanded statuses used by CamPay are writable.
        $paymentId = \DB::table('payments')->insertGetId([
            'deceased_id' => \DB::table('deceaseds')->insertGetId([
                'full_name' => 'Status Test',
                'gender' => 'male',
                'date_of_death' => now()->toDateString(),
                'admission_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            'amount' => 25,
            'balance' => 0,
            'payment_date' => now()->toDateString(),
            'receipt_number' => 'REC-STATUS-1',
            'payment_method' => 'mobile_money',
            'status' => 'pending',
            'confirmed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('payments')->where('id', $paymentId)->update(['status' => 'failed']);
        $this->assertSame('failed', \DB::table('payments')->where('id', $paymentId)->value('status'));

        \DB::table('payments')->where('id', $paymentId)->update(['status' => 'successful']);
        $this->assertSame('successful', \DB::table('payments')->where('id', $paymentId)->value('status'));
    }
}
