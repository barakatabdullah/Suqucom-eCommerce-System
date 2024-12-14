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
            $table->foreignId('user_id')->constrained();
            $table->longText('shipping_address')->nullable();
            $table->longText('additional_info')->nullable();
            $table->enum('order_from', ['web', 'phone', 'facebook', 'instagram', 'other'])->default('web');
            $table->enum('delivery_status', ['pending', 'processing', 'delivered', 'canceled'])->default('pending');
            $table->string('payment_type', 50)->default('cash_on_delivery');
            $table->enum('payment_status', ['pending', 'paid', 'canceled'])->default('pending');
            $table->decimal('grand_total', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
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
