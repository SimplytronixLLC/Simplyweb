<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->enum('email_status', ['active', 'bounced', 'invalid'])
                ->default('active')->after('email');
            $table->unsignedTinyInteger('bounce_count')->default(0)->after('email_status');
            $table->timestamp('last_bounced_at')->nullable()->after('bounce_count');

            $table->enum('manual_action', [
                'none', 'rfq_received', 'quote_sent', 'quote_signed',
                'quote_unsigned', 'no_quote', 'invalid_rfq',
            ])->default('none')->after('stage');
            $table->timestamp('manual_action_at')->nullable()->after('manual_action');
            $table->string('manual_action_note', 2000)->nullable()->after('manual_action_at');

            // Generalizes the old implicit "source == winback" cadence assumption.
            // Existing rows: backfill lead_source from source, cadence_type = 'winback'
            // where source = 'winback', else null. See down-migration note.
            $table->string('lead_source')->nullable()->after('source');
            $table->string('cadence_type')->nullable()->after('followup_step');

            // 30-day auto-enroll bookkeeping for web-form leads (see PLAN.md assumption)
            $table->timestamp('webform_followup_eligible_at')->nullable()->after('cadence_type');
        });

        Schema::table('crm_email_log', function (Blueprint $table) {
            $table->uuid('message_token')->nullable()->unique()->after('id');
        });

        Schema::create('crm_email_batches', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');
            $table->string('created_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->timestamps();
        });

        Schema::create('crm_email_batch_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('crm_email_batches')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->enum('status', ['queued', 'sent', 'bounced', 'skipped'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_batch_recipients');
        Schema::dropIfExists('crm_email_batches');

        Schema::table('crm_email_log', function (Blueprint $table) {
            $table->dropColumn('message_token');
        });

        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropColumn([
                'email_status', 'bounce_count', 'last_bounced_at',
                'manual_action', 'manual_action_at', 'manual_action_note',
                'lead_source', 'cadence_type', 'webform_followup_eligible_at',
            ]);
        });
    }
};
