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
            $table->dropColumn('default_picture_url');
            $table->after('markup_user', function (Blueprint $table) {
                $table->string('default_picture');
                $table->string('default_cover');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->after('markup_user', function (Blueprint $table) {
                $table->dropColumn('default_picture');
                $table->dropColumn('default_cover');
                $table->string('default_picture_url');
            });
        });
    }
};
