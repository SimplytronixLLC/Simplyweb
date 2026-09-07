<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('seo_audit_cache', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->string('product_key');
            $table->string('manufacturer')->nullable();
            $table->tinyInteger('has_image')->default(0);
            $table->tinyInteger('has_datasheet')->default(0);
            $table->tinyInteger('has_specs')->default(0);
            $table->tinyInteger('has_description')->default(0);
            $table->tinyInteger('has_meta_title')->default(0);
            $table->tinyInteger('has_meta_desc')->default(0);
            $table->tinyInteger('has_digikey_raw')->default(0);
            $table->tinyInteger('title_ok')->default(0);
            $table->tinyInteger('desc_ok')->default(0);
            $table->tinyInteger('schema_ok')->default(0);
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_audit_cache');
    }
};