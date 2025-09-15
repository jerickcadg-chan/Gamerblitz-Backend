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
            $table->string('provider_ref')->unique()->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_item_id');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->text('cust_account')->nullable();
            $table->bigInteger('cust_phone_number');
            $table->string('cust_email')->nullable();
            $table->string('payment_method', 25)->nullable();
            $table->string('provider');
            $table->enum('status', config('array.order.status'));
            $table->integer('qty');
            $table->decimal('price', 19, 2);
            $table->decimal('capital', 19, 2);
            $table->decimal('turnover', 19, 2);
            $table->decimal('admin_fee', 19, 2)->default(0);
            $table->decimal('discount_price', 19, 2)->default(0);
            $table->decimal('total_price', 19, 2);
            $table->decimal('total_income', 19, 2);
            $table->text('payment_url')->nullable();
            $table->text('payment_code')->nullable();
            $table->string('payment_id')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->char('currency_code', 3);
            $table->char('converted_currency_code', 3);
            $table->decimal('exchange_rate', 18, 8);
            $table->decimal('converted_price', 19, 2);
            $table->decimal('converted_capital', 19, 2);
            $table->decimal('converted_turnover', 19, 2);
            $table->decimal('converted_admin_fee', 19, 2)->default(0);
            $table->decimal('converted_discount_price', 19, 2)->default(0);
            $table->decimal('converted_total_price', 19, 2);
            $table->decimal('converted_total_income', 19, 2);
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
