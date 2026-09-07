<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cached_products', function (Blueprint $table) {

            $table->unsignedBigInteger('category_level_1')->nullable()->after('category_id');
            $table->unsignedBigInteger('category_level_2')->nullable()->after('category_level_1');
            $table->unsignedBigInteger('category_level_3')->nullable()->after('category_level_2');

            // optional indexes (recommended for speed)
            $table->index('category_level_1');
            $table->index('category_level_2');
            $table->index('category_level_3');
        });
    }

    public function down()
    {
        Schema::table('cached_products', function (Blueprint $table) {

            $table->dropIndex(['category_level_1']);
            $table->dropIndex(['category_level_2']);
            $table->dropIndex(['category_level_3']);

            $table->dropColumn([
                'category_level_1',
                'category_level_2',
                'category_level_3'
            ]);
        });
    }
};