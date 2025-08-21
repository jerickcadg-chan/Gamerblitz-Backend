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
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('capital');
            $table->after('price', function (Blueprint $table) {
                $table->double('price_silver')->default(0);
                $table->double('price_gold')->default(0);
                $table->double('price_vip')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('price_silver');
            $table->dropColumn('price_gold');
            $table->dropColumn('price_vip');
            $table->after('price', function (Blueprint $table) {
                $table->double('capital');
            });
        });
    }
};
