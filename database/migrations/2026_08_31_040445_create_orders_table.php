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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->unsignedBigInteger('shipping_rate_id')->nullable();
            $table->string('order_number', 50)->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('discount', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('payment_method', 100)->nullable();
            $table->dateTime('delivery_schedule')->nullable();
            $table->enum(
                'status',
                ['waiting_payment', 'processed', 'packing', 'shipping', 'completed', 'cancelled',]
            )->default('waiting_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
