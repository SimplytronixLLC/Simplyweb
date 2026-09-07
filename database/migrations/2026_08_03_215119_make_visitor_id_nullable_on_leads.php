<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE leads MODIFY visitor_id CHAR(36) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE leads MODIFY visitor_id CHAR(36) NOT NULL');
    }
};
