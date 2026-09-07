<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'source')) {
                $table->string('source')->nullable();
            }
            if (!Schema::hasColumn('leads', 'part_number')) {
                $table->string('part_number')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('leads', 'part_number')) {
                $table->dropColumn('part_number');
            }
        });
    }
};
