<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE visitor_profiles MODIFY ip_country VARCHAR(60) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE visitor_profiles MODIFY ip_country VARCHAR(2) NULL');
    }
};
