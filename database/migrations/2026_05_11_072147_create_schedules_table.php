<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deceased_id')->constrained('deceaseds')->cascadeOnDelete();
            $table->date('release_date')->nullable();
            $table->string('location')->nullable();
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->date('burial_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
