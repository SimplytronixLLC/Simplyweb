<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('bom_submissions', function (Blueprint $table) {
        $table->id();
        $table->string('bom_id')->unique();
        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->string('company');
        $table->string('filename');
        $table->text('comments')->nullable();
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
        Schema::dropIfExists('bom_submissions');
    }
}
