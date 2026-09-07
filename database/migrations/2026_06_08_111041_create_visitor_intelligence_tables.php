<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Enhanced visitor profile
        Schema::create('visitor_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->unique()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('ip_country', 2)->nullable();
            $table->string('ip_region', 100)->nullable();
            $table->string('ip_city', 100)->nullable();
            $table->string('ip_timezone', 50)->nullable();
            $table->decimal('ip_latitude', 10, 7)->nullable();
            $table->decimal('ip_longitude', 10, 7)->nullable();
            $table->string('ip_isp', 150)->nullable();
            $table->string('ip_org', 150)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('device_brand', 50)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('os_name', 50)->nullable();
            $table->string('os_version', 30)->nullable();
            $table->string('browser_name', 50)->nullable();
            $table->string('browser_version', 30)->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_tablet')->default(false);
            $table->boolean('is_bot')->default(false);
            $table->string('language', 5)->nullable();
            $table->integer('screen_width')->nullable();
            $table->integer('screen_height')->nullable();
            $table->string('timezone', 50)->nullable();
            $table->integer('engagement_score')->default(0)->index();
            $table->timestamp('first_visit_at')->nullable();
            $table->timestamp('last_visit_at')->nullable();
            $table->timestamps();
        });

        // Visitor engagement & behavior
        Schema::create('visitor_behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->unique()->index();
            $table->integer('total_visits')->default(1);
            $table->integer('total_pageviews')->default(0);
            $table->integer('total_searches')->default(0);
            $table->integer('product_views')->default(0);
            $table->integer('unique_products_viewed')->default(0);
            $table->integer('quote_requests')->default(0);
            $table->decimal('avg_time_on_site', 8, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->integer('most_viewed_category_id')->nullable();
            $table->string('most_viewed_manufacturer', 100)->nullable();
            $table->string('top_searched_keyword', 100)->nullable();
            $table->boolean('has_contacted_us')->default(false);
            $table->boolean('has_submitted_quote')->default(false);
            $table->boolean('has_viewed_datasheet')->default(false);
            $table->string('conversion_status', 30)->default('prospect');
            $table->timestamps();
            $table->foreign('visitor_id')->references('visitor_id')->on('visitor_profiles')->onDelete('cascade');
        });

        // Contact attempts & outreach
        Schema::create('visitor_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->index();
            $table->string('email', 150)->nullable()->index();
            $table->string('phone', 20)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('contact_source', 30)->nullable();
            $table->boolean('email_verified')->default(false);
            $table->boolean('opted_in_marketing')->default(false);
            $table->timestamp('email_collected_at')->nullable();
            $table->timestamps();
            $table->foreign('visitor_id')->references('visitor_id')->on('visitor_profiles')->onDelete('cascade');
        });

        // Outreach history
        Schema::create('visitor_outreach_logs', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->index();
            $table->string('email', 150)->nullable();
            $table->string('outreach_type', 30);
            $table->text('message_subject')->nullable();
            $table->text('message_body')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('sent_by', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('visitor_id')->references('visitor_id')->on('visitor_profiles')->onDelete('cascade');
        });

        // Interest tracking
        Schema::create('visitor_interests', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->index();
            $table->string('product_key', 100)->index();
            $table->string('manufacturer', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->integer('view_count')->default(1);
            $table->integer('time_spent_seconds')->default(0);
            $table->boolean('added_to_cart')->default(false);
            $table->boolean('inquired')->default(false);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamps();
            $table->foreign('visitor_id')->references('visitor_id')->on('visitor_profiles')->onDelete('cascade');
            $table->unique(['visitor_id', 'product_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_outreach_logs');
        Schema::dropIfExists('visitor_contacts');
        Schema::dropIfExists('visitor_interests');
        Schema::dropIfExists('visitor_behaviors');
        Schema::dropIfExists('visitor_profiles');
    }
};