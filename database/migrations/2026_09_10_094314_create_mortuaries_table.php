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
    Schema::create('mortuaries', function (Blueprint $table) {
        $table->id();

        $table->string('name');

        $table->string('city');

        $table->string('quarter');

        $table->string('address')->nullable();

        $table->decimal('latitude', 10, 7);

        $table->decimal('longitude', 10, 7);

        $table->text('description')->nullable();

        $table->string('phone')->nullable();

        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortuaries');
    }
};
