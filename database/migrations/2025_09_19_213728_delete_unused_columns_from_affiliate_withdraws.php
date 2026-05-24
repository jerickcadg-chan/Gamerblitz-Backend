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
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->dropColumn('method');
            $table->dropColumn('destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            $table->string('method', 50)->nullable();
            $table->string('destination', 191)->nullable();
            $table->text('notes')->nullable();
        });
    }
};
