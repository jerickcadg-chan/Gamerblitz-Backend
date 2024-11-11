<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_item_id');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->string('cust_account')->nullable();
            $table->bigInteger('cust_phone_number');
            $table->string('cust_email')->nullable();
            $table->string('payment_method', 25)->nullable();
            $table->enum('payment_status', ['pending', 'settlement', 'refunded']);
            $table->enum('order_status', ['waiting-payment', 'in-process', 'done', 'expired', 'canceled']);
            $table->integer('qty');
            $table->decimal('price', 19, 2);
            $table->decimal('capital', 19, 2);
            $table->decimal('admin_fee', 19, 2)->default(0);
            $table->decimal('discount_price', 19, 2)->default(0);
            $table->decimal('total_price', 19, 2);
            $table->decimal('total_income', 19, 2);
            $table->string('payment_url')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('product_item_id')->references('id')->on('product_items')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('discount_id')->references('id')->on('discounts')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
