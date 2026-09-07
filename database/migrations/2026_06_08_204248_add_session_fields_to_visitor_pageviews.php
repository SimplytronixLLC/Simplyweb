<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('visitor_pageviews', function (Blueprint $table) {
            $table->string('session_id', 36)->nullable()->after('visitor_id')->index();
            $table->integer('time_on_page')->nullable()->after('browser'); // seconds
        });

        Schema::table('visitor_profiles', function (Blueprint $table) {
            $table->string('first_referrer', 500)->nullable()->after('language');
            $table->string('first_landing_page', 500)->nullable()->after('first_referrer');
            $table->string('first_utm_source', 100)->nullable()->after('first_landing_page');
            $table->string('first_utm_medium', 100)->nullable()->after('first_utm_source');
            $table->string('first_utm_campaign', 100)->nullable()->after('first_utm_medium');
            $table->integer('total_session_seconds')->default(0)->after('engagement_score');
        });
    }

    public function down()
    {
        Schema::table('visitor_pageviews', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'time_on_page']);
        });
        Schema::table('visitor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'first_referrer', 'first_landing_page',
                'first_utm_source', 'first_utm_medium', 'first_utm_campaign',
                'total_session_seconds'
            ]);
        });
    }
};