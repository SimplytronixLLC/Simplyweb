<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {

            $table->id();

            $table->string('company_name');

            $table->string('contact_name')->nullable();

            $table->string('email')->unique();

            $table->string('phone')->nullable();

            $table->string('country')->nullable();

            $table->enum(
                'status',
                ['pending','approved','rejected']
            )->default('pending');

            $table->string('password');

            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};