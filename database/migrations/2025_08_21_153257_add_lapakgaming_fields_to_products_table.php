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
        Schema::table('products', function (Blueprint $table) {
            $table->string('provider'); // lapakgaming, manual, etc
            $table->string('provider_code'); // lapakgaming, manual, etc
            $table->string('provider_country'); // id, my, ph, etc
            $table->decimal('markup_reseller_silver_silver', 19, 2)->default(0);
            $table->decimal('markup_reseller_silver_gold', 19, 2)->default(0);
            $table->decimal('markup_reseller_silver_vip', 19, 2)->default(0);

            $table->dropColumn('markup_reseller_silver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('provider');
            $table->dropColumn('provider_code');
            $table->dropColumn('provider_country');
            $table->dropColumn('markup_reseller_silver_silver');
            $table->dropColumn('markup_reseller_silver_gold');
            $table->dropColumn('markup_reseller_silver_vip');

            $table->decimal('markup_reseller_silver', 19, 2)->default(0);
        });
    }
};
