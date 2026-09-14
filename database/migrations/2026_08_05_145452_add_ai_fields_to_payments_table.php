<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->string('campay_reference')->nullable();

            $table->string('phone_number')->nullable();

            $table->string('payer_name')->nullable();

            $table->string('receipt_pdf')->nullable();

            $table->string('qr_code')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropColumn([
                'campay_reference',
                'phone_number',
                'payer_name',
                'receipt_pdf',
                'qr_code'
            ]);

        });
    }
};