<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * crm_activities
     *
     * Timeline table — every auto-email, manual note, call log, and stage
     * change gets a row here, per contact. This is what powers the pipeline
     * card's activity feed and gives you an audit trail for automation.
     */
    public function up(): void
    {
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crm_contact_id')
                  ->constrained('crm_contacts')
                  ->cascadeOnDelete();

            // 'auto_email' | 'manual_note' | 'call_log' | 'stage_change' | 'winback_email'
            $table->string('type')->index();

            // For emails: subject/body sent. For stage_change: old_stage -> new_stage in meta.
            $table->string('subject')->nullable();
            $table->text('body')->nullable();

            // Free-form JSON for type-specific extras (old_stage, new_stage, call outcome, etc.)
            $table->json('meta')->nullable();

            // Who/what performed it: 'system' for automation, or an admin user identifier
            $table->string('performed_by')->default('system');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
