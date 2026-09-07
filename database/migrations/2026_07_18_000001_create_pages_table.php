<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();       // matches keys in config/page_schemas.php
            $table->string('title')->nullable();     // internal label, not the page's <title>
            $table->json('content')->nullable();     // all editable field values, keyed by field key
            $table->json('meta')->nullable();         // reserved for future SEO overrides
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
