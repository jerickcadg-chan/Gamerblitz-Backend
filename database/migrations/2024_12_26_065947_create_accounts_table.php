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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_item_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->string('title');
            $table->text('information');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->text('description');
            $table->double('winrate')->default(0);
            $table->integer('skin')->default(0);
            $table->integer('heroes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
