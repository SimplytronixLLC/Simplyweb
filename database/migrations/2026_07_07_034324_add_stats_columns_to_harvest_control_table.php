<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('harvest_control', function (Blueprint $table) {
            $table->integer('total_fetched')->default(0);
            $table->integer('total_inserted')->default(0);
            $table->string('stop_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('harvest_control', function (Blueprint $table) {
            $table->dropColumn(['total_fetched', 'total_inserted', 'stop_reason']);
        });
    }
};
