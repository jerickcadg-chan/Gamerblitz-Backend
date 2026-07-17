<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * When is_auto_disabled = 1:
     *   The product was disabled automatically by the GPDS/Whitelabel sync
     *   because it was missing or inactive in the GPDS source.
     *   It can be re-enabled automatically when GPDS re-enables it.
     *
     * When is_auto_disabled = 0 (default):
     *   The product is either active, or was manually disabled by an admin.
     *   Manual disables are never overridden by the sync.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_auto_disabled')->default(false)->after('status')
                ->comment('True if product was disabled automatically by GPDS sync; false if active or manually disabled by admin.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_auto_disabled');
        });
    }
};
