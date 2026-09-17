<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'campay_reference')) {
                $table->string('campay_reference')->nullable();
            }

            if (!Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }

            if (!Schema::hasColumn('payments', 'payer_name')) {
                $table->string('payer_name')->nullable();
            }

            if (!Schema::hasColumn('payments', 'receipt_pdf')) {
                $table->string('receipt_pdf')->nullable();
            }

            if (!Schema::hasColumn('payments', 'qr_code')) {
                $table->string('qr_code')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['campay_reference', 'phone_number', 'payer_name', 'receipt_pdf', 'qr_code'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
