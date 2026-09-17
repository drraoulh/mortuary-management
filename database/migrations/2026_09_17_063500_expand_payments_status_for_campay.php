<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        /*
        | MySQL ENUM was limited to pending/confirmed, which breaks
        | CamPay flows that use successful/failed.
        */
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY status VARCHAR(32) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE payments ALTER COLUMN status TYPE VARCHAR(32)');
            DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'pending'");
        }
        // sqlite already uses a flexible string column in create_payments.

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'campay_status')) {
                $table->string('campay_status')->nullable()->after('status');
            }

            if (!Schema::hasColumn('payments', 'campay_reference')) {
                $table->string('campay_reference')->nullable()->after('campay_status');
            }

            if (!Schema::hasColumn('payments', 'campay_operator_reference')) {
                $table->string('campay_operator_reference')->nullable()->after('campay_reference');
            }

            if (!Schema::hasColumn('payments', 'mobile_operator')) {
                $table->string('mobile_operator')->nullable();
            }

            if (!Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Map extended values back before shrinking the enum.
            DB::table('payments')->where('status', 'successful')->update(['status' => 'confirmed']);
            DB::table('payments')->where('status', 'failed')->update(['status' => 'pending']);
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending','confirmed') NOT NULL DEFAULT 'pending'");
        }
    }
};
