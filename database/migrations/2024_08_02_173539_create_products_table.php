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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description');
            $table->decimal('purchase_price', 8, 2);
            $table->boolean('published')->default(true);
            $table->string('slug')->unique();
            $table->boolean('approved')->default(false);
            $table->foreignId('brand_id')->nullable()->constrained();
            $table->boolean('cash_on_delivery')->default(false);
            $table->boolean('free_shipping')->default(false);
            $table->foreignId('user_id')->constrained();
            $table->integer('stock')->default(0);
            $table->string('discount_type', 50)->default('percentage')->nullable();
            $table->decimal('discount', 8, 2)->default(0);
            $table->timestamp('discount_start')->nullable();
            $table->timestamp('discount_end')->nullable();
            $table->string('external_link')->nullable();
            $table->decimal('num_of_sales', 8, 2)->default(0);
            $table->integer('rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
