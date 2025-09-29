<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropForeign(['product_item_category_meta_id']);

            $table->foreign('product_item_category_meta_id')
                ->references('id')
                ->on('product_item_category_metas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropForeign(['product_item_category_meta_id']);

            $table->foreign('product_item_category_meta_id')
                ->references('id')
                ->on('product_categories')
                ->cascadeOnDelete();
        });
    }
};
