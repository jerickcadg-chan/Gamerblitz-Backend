<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('ordering')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('input_format')->nullable();
            $table->foreignId('product_category_id')->constrained();
            $table->text('description');
            $table->string('company')->nullable();
            $table->text('how_to_order');
            $table->string('slug');
            $table->enum('status', ['active', 'inactive', 'not_visible']);
            $table->string('provider'); // lapakgaming, manual, etc
            $table->string('provider_code')->nullable(); // product code from provider ML, VAL, FF etc
            $table->string('provider_country')->nullable(); // id, my, ph, etc
            $table->decimal('markup_user', 19, 2)->default(0);
            $table->decimal('markup_reseller_silver', 19, 2)->default(0);
            $table->decimal('markup_reseller_gold', 19, 2)->default(0);
            $table->decimal('markup_reseller_vip', 19, 2)->default(0);
            $table->string('default_picture')->nullable();
            $table->string('default_cover')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
