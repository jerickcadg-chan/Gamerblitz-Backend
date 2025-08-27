<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->decimal('admin_fee', 19, 2)->default(0);
            $table->enum('admin_type', ['nominal', 'percentage', 'no-admin']);
            $table->string('vendor', 50);
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('ordering')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
