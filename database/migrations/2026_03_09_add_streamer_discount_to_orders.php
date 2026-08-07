<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'streamer_discount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('streamer_discount', 12, 2)->default(0)->after('discount_price');
            });
        }
    }
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'streamer_discount')) {
                $table->dropColumn('streamer_discount');
            }
        });
    }
};
