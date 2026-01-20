<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add ecommerce_order_id to orders table
        if (!Schema::hasColumn('orders', 'ecommerce_order_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('ecommerce_order_id')->nullable()->after('user_id');
            });
        }

        // Add payment_order_id to ecommerce_orders table
        if (!Schema::hasColumn('ecommerce_orders', 'payment_order_id')) {
            Schema::table('ecommerce_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('payment_order_id')->nullable()->after('user_id');
            });
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('ecommerce_order_id');
        });
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            $table->dropColumn('payment_order_id');
        });
    }
};
