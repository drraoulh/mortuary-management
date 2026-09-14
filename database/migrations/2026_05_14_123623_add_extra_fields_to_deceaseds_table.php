<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('deceaseds', function (Blueprint $table) {
        $table->date('date_of_birth')->nullable();
        $table->date('release_date')->nullable();
        $table->string('room_name')->nullable();
        $table->string('room_type')->default('normal');
        $table->decimal('price', 10, 2)->default(0);
        $table->string('security_key')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deceaseds', function (Blueprint $table) {
            //
        });
    }
};
