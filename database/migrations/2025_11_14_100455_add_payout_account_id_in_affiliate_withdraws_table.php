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
            if (!Schema::hasColumn('affiliate_withdraws', 'payout_account_id')) {
                $table->unsignedBigInteger('payout_account_id')->nullable()->after('id');
            }

            $table->foreign('payout_account_id', 'affiliate_withdraws_payout_fk')
                ->references('id')
                ->on('payout_accounts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            if (Schema::hasColumn('affiliate_withdraws', 'payout_account_id')) {
                $table->dropForeign(['payout_account_id']);
            }
        });
    }
};
