<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropUnique(['code']); // drop old unique index
            $table->unique(['code', 'country_code', 'provider']); // new composite unique
        });
    }

    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropUnique(['code', 'country_code', 'provider']);
            $table->unique('code');
        });
    }
};

