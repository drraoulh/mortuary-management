<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funeral_notices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('deceased_id')
                  ->constrained('deceaseds')
                  ->cascadeOnDelete();

            $table->text('announcement');

            $table->string('theme')->default('Classic');

            $table->string('language')->default('English');

            $table->string('pdf')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funeral_notices');
    }
};