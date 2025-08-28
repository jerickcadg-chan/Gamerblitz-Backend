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
            $table->string('currency_code', 3);  // target currency code (e.g., PHP, IDR)
            $table->decimal('rate', 20, 8);      // 1 USD = rate in currency_code
            $table->timestamp('effective_at');   // when this rate becomes effective
            $table->timestamps();

            $table->unique(['currency_code', 'effective_at']);
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
