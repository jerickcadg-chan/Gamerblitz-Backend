<?php

use App\Constants\ProductConstant;
use App\Models\Product;
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
            $table->enum('type', ['account', 'topup'])->default('topup')->after('capital');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['game', 'voucher', 'operator', 'other', 'account'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Product::whereCategory(ProductConstant::ACCOUNT)->update(['category' => 'other']);
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['game', 'voucher', 'operator', 'other'])->default('other')->change();
        });
    }
};
