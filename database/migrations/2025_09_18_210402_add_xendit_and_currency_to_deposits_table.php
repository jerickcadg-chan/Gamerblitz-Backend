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
        Schema::table('deposits', function (Blueprint $table) {
            $table->text('payment_url')->nullable();
            $table->text('payment_code')->nullable();
            $table->string('payment_descriptor')->nullable();
            $table->string('payment_id')->nullable();
            $table->char('currency_code', 3);
            $table->char('converted_currency_code', 3);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('converted_amount', 18, 8);
            $table->decimal('converted_unique_code', 18, 8);
            $table->decimal('converted_total_amount', 19, 2);
            $table->decimal('admin_fee', 18, 8);
            $table->decimal('converted_admin_fee', 18, 8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('payment_url');
            $table->dropColumn('payment_code');
            $table->dropColumn('payment_descriptor');
            $table->dropColumn('payment_id');
            $table->dropColumn('exchange_rate');
            $table->dropColumn('currency_code');
            $table->dropColumn('admin_fee');
            $table->dropColumn('converted_admin_fee');
            $table->dropColumn('converted_currency_code');
            $table->dropColumn('converted_amount');
            $table->dropColumn('converted_unique_code');
            $table->dropColumn('converted_total_amount');
        });
    }
};
