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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productcategory_id');
            $table->unsignedBigInteger('productstate_id');
            $table->string('name');
            $table->text('detail');
            $table->decimal('value', 10, 2);
            $table->string('brand');
            $table->string('image');
            $table->boolean('soldflg')->default(false);
            $table->timestamps();

            $table->foreign('productcategory_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('productstate_id')->references('id')->on('product_states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
