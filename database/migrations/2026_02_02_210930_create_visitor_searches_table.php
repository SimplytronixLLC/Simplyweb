<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorSearchesTable extends Migration
{
    public function up()
    {
        Schema::create('visitor_searches', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_id')->index();
            $table->string('part_number')->index();
            $table->string('source')->nullable(); // search, rfq, product_page
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_searches');
    }
}
