<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_products', function (Blueprint $table) {
        $table->id();
        $table->string('product_key')->unique(); // DigiKeyProductNumber
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('image')->nullable();
        $table->string('category')->nullable();
        $table->string('manufacturer')->nullable();
        $table->decimal('unit_price', 10, 2)->nullable();
        $table->integer('quantity');
        $table->json('raw_data')->nullable();
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
        Schema::dropIfExists('cached_products');
    }
}
