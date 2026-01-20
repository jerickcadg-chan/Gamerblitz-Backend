<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('balance_id');
            $table->morphs('balanceable');
            $table->text('description');
            $table->double('amount');
            $table->double('latest_balance');
            $table->timestamps();

            $table->foreign('balance_id')->references('id')->on('balances')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_histories');
    }
}
