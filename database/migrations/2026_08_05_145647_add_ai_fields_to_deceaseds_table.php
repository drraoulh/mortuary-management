<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deceaseds', function (Blueprint $table) {

            $table->string('photo')->nullable();

            $table->string('grave_location')->nullable();

            $table->decimal('latitude',10,7)->nullable();

            $table->decimal('longitude',10,7)->nullable();

            $table->string('qr_code')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('deceaseds', function (Blueprint $table) {

            $table->dropColumn([
                'photo',
                'grave_location',
                'latitude',
                'longitude',
                'qr_code'
            ]);

        });
    }
};