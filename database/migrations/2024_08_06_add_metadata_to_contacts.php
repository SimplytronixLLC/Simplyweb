<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = 'crm_contacts';  // ← Changed from 'contacts' to 'crm_contacts'
        
        // Add metadata column if it doesn't exist
        if (!Schema::hasColumn($table, 'metadata')) {
            DB::statement('ALTER TABLE `' . $table . '` ADD COLUMN `metadata` JSON NULL AFTER `notes`');
        }

        // Add last_contact_date if it doesn't exist
        if (!Schema::hasColumn($table, 'last_contact_date')) {
            DB::statement('ALTER TABLE `' . $table . '` ADD COLUMN `last_contact_date` TIMESTAMP NULL AFTER `updated_at`');
        }
    }
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['metadata', 'last_contact_date']);
        });
    }
};