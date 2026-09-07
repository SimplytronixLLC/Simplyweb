<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // crm_contacts columns

        if (!Schema::hasColumn('crm_contacts', 'email_status')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->enum('email_status', ['active','bounced','invalid'])
                    ->default('active')
                    ->after('email');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'bounce_count')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->unsignedTinyInteger('bounce_count')
                    ->default(0)
                    ->after('email_status');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'last_bounced_at')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->timestamp('last_bounced_at')
                    ->nullable()
                    ->after('bounce_count');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'manual_action')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->enum('manual_action', [
                    'none',
                    'rfq_received',
                    'quote_sent',
                    'quote_signed',
                    'quote_unsigned',
                    'no_quote',
                    'invalid_rfq'
                ])->default('none')->after('stage');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'manual_action_at')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->timestamp('manual_action_at')
                    ->nullable()
                    ->after('manual_action');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'manual_action_note')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->string('manual_action_note',2000)
                    ->nullable()
                    ->after('manual_action_at');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'lead_source')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->string('lead_source')
                    ->nullable()
                    ->after('source');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'cadence_type')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->string('cadence_type')
                    ->nullable()
                    ->after('followup_step');
            });
        }

        if (!Schema::hasColumn('crm_contacts', 'webform_followup_eligible_at')) {
            Schema::table('crm_contacts', function (Blueprint $table) {
                $table->timestamp('webform_followup_eligible_at')
                    ->nullable()
                    ->after('cadence_type');
            });
        }

        // crm_activities instead of crm_email_log

        if (Schema::hasTable('crm_activities')
            && !Schema::hasColumn('crm_activities', 'message_token')) {

            Schema::table('crm_activities', function (Blueprint $table) {
                $table->uuid('message_token')
                    ->nullable()
                    ->unique()
                    ->after('crm_contact_id');
            });
        }

        if (!Schema::hasTable('crm_email_batches')) {
            Schema::create('crm_email_batches', function (Blueprint $table) {
                $table->id();
                $table->string('subject');
                $table->longText('body');
                $table->string('created_by')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->unsignedInteger('recipient_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_email_batch_recipients')) {
            Schema::create('crm_email_batch_recipients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_id')->constrained('crm_email_batches')->cascadeOnDelete();
                $table->foreignId('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
                $table->enum('status', ['queued','sent','bounced','skipped'])->default('queued');
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_batch_recipients');
        Schema::dropIfExists('crm_email_batches');

        if (Schema::hasTable('crm_activities') &&
            Schema::hasColumn('crm_activities','message_token')) {

            Schema::table('crm_activities', function (Blueprint $table) {
                $table->dropColumn('message_token');
            });
        }
    }
};