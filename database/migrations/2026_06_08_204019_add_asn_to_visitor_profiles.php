<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('visitor_profiles', function (Blueprint $table) {
            $table->string('ip_asn', 20)->nullable()->after('ip_org');
            $table->string('ip_asname', 150)->nullable()->after('ip_asn');
            $table->string('ip_country_code', 2)->nullable()->after('ip_country');
        });
    }

    public function down()
    {
        Schema::table('visitor_profiles', function (Blueprint $table) {
            $table->dropColumn(['ip_asn', 'ip_asname', 'ip_country_code']);
        });
    }
};