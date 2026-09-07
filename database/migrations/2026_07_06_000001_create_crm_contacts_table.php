<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * crm_contacts
     *
     * One row per real person/company. A contact can originate from any
     * (or multiple) of the three existing lead sources — quote, visitor
     * intelligence, or Dolibarr order history — via nullable FKs below.
     * We intentionally do NOT enforce a foreign key constraint against the
     * `dolibarr_thirdparty_id` column since Dolibarr lives in a separate
     * database/connection; it's a soft reference only.
     */
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();

            // Soft links back to source systems (same DB unless noted)
            $table->unsignedBigInteger('quote_id')->nullable()->index();
            $table->unsignedBigInteger('visitor_id')->nullable()->index();
            $table->unsignedBigInteger('dolibarr_thirdparty_id')->nullable()->index(); // separate DB — no FK constraint

            // Pipeline state
            $table->enum('stage', ['new', 'contacted', 'quoted', 'won', 'lost'])
                  ->default('new')
                  ->index();

            // Engagement / scoring (pulled from visitor_profiles when available)
            $table->unsignedInteger('engagement_score')->default(0);

            // Automation control
            $table->boolean('automation_enabled')->default(true);
            $table->unsignedInteger('followup_step')->default(0);
            $table->timestamp('next_followup_at')->nullable()->index();
            $table->timestamp('last_contacted_at')->nullable();

            // Origin / segment tagging (e.g. 'quote', 'visitor', 'winback')
            $table->string('source')->default('quote')->index();

            // Free-form notes for manual entries
            $table->text('notes')->nullable();

            $table->timestamps();

            // A given source record should map to at most one CRM contact
            $table->unique('quote_id');
            $table->unique('visitor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
