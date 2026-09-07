<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('duplicate_of_id')->nullable()->after('notes')->index();
            $table->boolean('duplicate_reviewed')->default(false)->after('duplicate_of_id');
        });
    }
    public function down(): void
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropColumn(['duplicate_of_id', 'duplicate_reviewed']);
        });
    }
};
