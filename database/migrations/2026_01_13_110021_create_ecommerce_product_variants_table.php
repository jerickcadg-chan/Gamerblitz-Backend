<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecommerce_product_variants', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('product_id')
                ->constrained('ecommerce_products')
                ->cascadeOnDelete();

            // Variant info
            $table->string('name'); // e.g. Size M, Red, Digital Code
            $table->string('sku')->unique();

            // Pricing
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();

            // Inventory
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_variants');
    }
};
