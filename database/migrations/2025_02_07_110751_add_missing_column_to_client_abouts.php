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
        Schema::table('client_abouts', function (Blueprint $table) {
            $table->string('mgclient_level')->nullable();
            $table->string('gtm_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_abouts', function (Blueprint $table) {
            $table->dropColumn('mgclient_level');
            $table->dropColumn('gtm_id');
        });
    }
};
