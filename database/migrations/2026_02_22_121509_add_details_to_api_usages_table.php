<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToApiUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('api_usages', function (Blueprint $table) {
        $table->string('endpoint')->nullable();
        $table->string('query')->nullable();
        $table->string('controller')->nullable();
        $table->string('ip_address')->nullable();
        $table->text('user_agent')->nullable();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_usages', function (Blueprint $table) {
            //
        });
    }
}
