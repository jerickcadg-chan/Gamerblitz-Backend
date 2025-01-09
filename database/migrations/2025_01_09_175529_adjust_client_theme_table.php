<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_themes', function (Blueprint $table) {
            $table->string('mg_accent_success')->nullable();
            $table->string('mg_accent_error')->nullable();
            $table->string('mg_accent_warning')->nullable();
            $table->dropColumn('mg_fg_error');
            $table->dropColumn('mg_status_waiting');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_themes', function (Blueprint $table) {
            $table->dropColumn('mg_accent_success');
            $table->dropColumn('mg_accent_error');
            $table->dropColumn('mg_accent_warning');
            $table->string('mg_fg_error')->nullable();
            $table->string('mg_status_waiting')->nullable();
        });
    }
};
