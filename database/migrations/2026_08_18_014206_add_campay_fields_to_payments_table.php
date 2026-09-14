<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('payments', function (Blueprint $table) {

        if (!Schema::hasColumn('payments', 'mobile_operator')) {
            $table->string('mobile_operator')->nullable();
        }

        if (!Schema::hasColumn('payments', 'campay_reference')) {
            $table->string('campay_reference')->nullable();
        }

        if (!Schema::hasColumn('payments', 'campay_status')) {
            $table->string('campay_status')->nullable();
        }

    });
}
    

   public function down(): void
{
    Schema::table('payments', function (Blueprint $table) {

        if (Schema::hasColumn('payments', 'mobile_operator')) {
            $table->dropColumn('mobile_operator');
        }

        if (Schema::hasColumn('payments', 'campay_reference')) {
            $table->dropColumn('campay_reference');
        }

        if (Schema::hasColumn('payments', 'campay_status')) {
            $table->dropColumn('campay_status');
        }

    });
}
};