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
    Schema::create('deceaseds', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('gender');
        $table->date('date_of_death');
        $table->string('cause_of_death');
        $table->date('admission_date');
        $table->timestamps();
    });
}
        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deceaseds');
    }
};
