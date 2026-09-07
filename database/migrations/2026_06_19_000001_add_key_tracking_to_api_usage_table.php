<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_usages', function (Blueprint $table) {
            // Which key pair (0-based index) made this call. Null for
            // providers without key rotation (e.g. Mouser).
            $table->unsignedTinyInteger('key_index')->nullable()->after('controller');

            // HTTP status code returned by the call. Lets us filter for
            // 429s, 401s, etc. without re-parsing logs.
            $table->unsignedSmallInteger('status_code')->nullable()->after('key_index');

            // Retry-After value (in seconds) if the response was a 429.
            // Null if not a 429 or no header was present.
            $table->unsignedInteger('retry_after_seconds')->nullable()->after('status_code');

            $table->index(['provider', 'key_index']);
            $table->index(['provider', 'status_code']);
        });
    }

    public function down(): void
    {
        Schema::table('api_usages', function (Blueprint $table) {
            $table->dropIndex(['provider', 'key_index']);
            $table->dropIndex(['provider', 'status_code']);
            $table->dropColumn(['key_index', 'status_code', 'retry_after_seconds']);
        });
    }
};