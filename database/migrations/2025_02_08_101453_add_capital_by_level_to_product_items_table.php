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
                $table->double('capital_silver')->default(0);
                $table->double('capital_gold')->default(0);
                $table->double('capital_platinum')->default(0);
                $table->double('capital_diamond')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('capital_silver');
            $table->dropColumn('capital_gold');
            $table->dropColumn('capital_platinum');
            $table->dropColumn('capital_diamond');
            $table->after('price', function (Blueprint $table) {
                $table->double('capital');
            });
        });
    }
};
