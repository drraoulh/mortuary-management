<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Accounts created without an explicit role get the least privileged one.
        // Existing rows keep their current role.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // The original enum only allowed "pending" and "confirmed",
            // but CamPay payments are also stored as "successful" and "failed".
            $table->string('status')->default('pending')->change();
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'status')) {
                $table->string('status')->default('pending');
            }

            $table->string('location')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff')->change();
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
