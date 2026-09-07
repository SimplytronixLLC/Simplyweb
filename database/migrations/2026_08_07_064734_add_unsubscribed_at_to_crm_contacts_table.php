<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnsubscribedAtToCrmContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->timestamp('unsubscribed_at')->nullable()->after('email_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropColumn('unsubscribed_at');
        });
    }
}
