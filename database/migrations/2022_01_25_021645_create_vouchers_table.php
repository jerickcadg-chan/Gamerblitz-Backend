<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_item_id');
            $table->string('serial_number');
            $table->text('password');
            $table->decimal('capital', 19, 2);
            $table->string('vendor', 25)->nullable();
            $table->enum('status', ['ready', 'used', 'broken'])->default('ready');
            $table->timestamps();

            $table->foreign('product_item_id')->references('id')->on('product_items')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
