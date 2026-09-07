<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('visitor_pageviews', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->index();
            $table->string('url', 500);
            $table->string('page_type', 30)->nullable(); // home, product, category, blog, quote, other
            $table->string('product_key', 100)->nullable()->index();
            $table->string('manufacturer', 100)->nullable()->index();
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('device', 20)->nullable(); // desktop, mobile, tablet
            $table->string('browser', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->unique();
            $table->string('first_referrer', 500)->nullable();
            $table->string('first_utm_source', 100)->nullable();
            $table->string('first_utm_medium', 100)->nullable();
            $table->string('first_utm_campaign', 100)->nullable();
            $table->string('first_landing_page', 500)->nullable();
            $table->string('device', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('ip', 45)->nullable();
            $table->integer('total_pageviews')->default(0);
            $table->integer('total_searches')->default(0);
            $table->boolean('converted_to_quote')->default(false);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_pageviews');
        Schema::dropIfExists('visitor_sessions');
    }
};