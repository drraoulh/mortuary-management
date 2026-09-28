<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deceased records a family / client account has unlocked with a verification key.
     */
    public function up(): void
    {
        Schema::create('deceased_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deceased_id')->constrained('deceaseds')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'deceased_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deceased_user');
    }
};
