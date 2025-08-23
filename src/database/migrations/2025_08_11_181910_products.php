<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Products extends Migration
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
            $table->unsignedBigInteger('productcategory_id');
            $table->unsignedBigInteger('productstate_id');
            $table->unsignedBigInteger('productbrand_id');
            $table->string('name');
            $table->text('detail');
            $table->decimal('value', 10, 2);
            $table->string('image')->nullable();
            $table->boolean('soldflg')->default(false);
            $table->timestamps();

            $table->foreign('productcategory_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('productstate_id')->references('id')->on('product_states')->onDelete('cascade');
            $table->foreign('productbrand_id')->references('id')->on('product_brands')->onDelete('cascade');
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
