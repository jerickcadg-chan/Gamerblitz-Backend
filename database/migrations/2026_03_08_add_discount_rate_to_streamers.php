<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->decimal('discount_rate', 5, 2)->default(0.5)->after('commission_rate')->comment('Discount rate (%) for streamer codes, default 0.5%');
        });
    }

    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn('discount_rate');
        });
    }
};
