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
        Schema::table('affiliate_histories', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->double('latest_balance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_histories', function (Blueprint $table) {
            if (Schema::hasColumn('affiliate_histories', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('affiliate_histories', 'latest_balance')) {
                $table->dropColumn('latest_balance');
            }
        });
    }
};
