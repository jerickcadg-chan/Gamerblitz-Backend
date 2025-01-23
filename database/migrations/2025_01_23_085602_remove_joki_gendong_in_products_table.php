<?php

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
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['game', 'voucher', 'operator', 'other', 'account', 'joki_gendong', 'joki'])->change();
        });
        Product::whereCategory('joki_gendong')->update([
            'category' => 'joki'
        ]);
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['game', 'voucher', 'operator', 'other', 'account', 'joki'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
