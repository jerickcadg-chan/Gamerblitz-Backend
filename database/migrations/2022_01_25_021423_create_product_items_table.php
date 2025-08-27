<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('stock')->nullable();
            $table->decimal('capital', 19, 2);
            $table->decimal('margin', 19, 2)->default(0);
            $table->decimal('margin_silver', 19, 2)->default(0);
            $table->decimal('margin_gold', 19, 2)->default(0);
            $table->decimal('margin_vip', 19, 2)->default(0);
            $table->enum('status', ['active','non-active','trouble','empty'])->default('active');
            $table->timestamp('sync_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')
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
        Schema::dropIfExists('product_items');
    }
}
