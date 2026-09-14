<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deceased_id')
                ->constrained('deceaseds')
                ->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('receipt_number')->unique();
            $table->string('transaction_id')->nullable();

$table->string('payment_method')->default('Campay');

$table->timestamp('confirmed_at')->nullable();
            $table->enum('status', [
    'pending',
    'confirmed'
])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};