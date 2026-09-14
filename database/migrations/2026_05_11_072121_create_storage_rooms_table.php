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
        Schema::create('storage_rooms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('room_number');
            $table->integer('capacity');
            $table->string('status')->default('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_rooms');
    }
};
