<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecsToCachedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('cached_products', function (Blueprint $table) {
        $table->json('specs')->nullable();
        $table->boolean('specs_synced')->default(false);
    });
}

public function down()
{
    Schema::table('cached_products', function (Blueprint $table) {
        $table->dropColumn(['specs', 'specs_synced']);
    });
}
}
