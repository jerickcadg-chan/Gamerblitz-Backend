<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add option_id, image, and position to existing variants table
        Schema::table('ecommerce_product_variants', function (Blueprint $table) {
            $table->foreignId('variant_option_id')
                ->nullable()
                ->after('product_id')
                ->constrained('ecommerce_variant_options')
                ->nullOnDelete();
            $table->string('image')->nullable()->after('stock');
            $table->integer('position')->default(0)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('ecommerce_product_variants', function (Blueprint $table) {
            $table->dropForeign(['variant_option_id']);
            $table->dropColumn(['variant_option_id', 'image', 'position']);
        });
    }
};
