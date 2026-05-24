<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3);
            $table->string('target_currency', 3);
            $table->decimal('rate', 20, 8);      // N rate in currency_code = 1 target_currency
            $table->timestamp('effective_at');   // when this rate becomes effective
            $table->timestamps();

            $table->unique(['currency_code', 'target_currency', 'effective_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
