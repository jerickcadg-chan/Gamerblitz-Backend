<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If table already exists, do nothing (safety for production)
        if (Schema::hasTable('ecommerce_order_items')) {
            return;
        }

        Schema::create('ecommerce_order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ecommerce_order_id');
            $table->unsignedBigInteger('ecommerce_product_id');

            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2);

            $table->timestamps();

            // Foreign keys
            $table->foreign('ecommerce_order_id')
                ->references('id')
                ->on('ecommerce_orders')
                ->cascadeOnDelete();

            $table->foreign('ecommerce_product_id')
                ->references('id')
                ->on('ecommerce_products')
                ->cascadeOnDelete();

            // Short index name (IMPORTANT)
            $table->index(
                ['ecommerce_order_id', 'ecommerce_product_id'],
                'eco_order_product_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_items');
    }
};
