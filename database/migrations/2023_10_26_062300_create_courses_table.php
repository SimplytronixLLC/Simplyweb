<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->string('image')->nullable();
			$table->string('course_price')->nullable();
			$table->string('course_time')->nullable();
			$table->string('status')->nullable();
			$table->text('description')->nullable();
			$table->text('short_description')->nullable();
			$table->text('meta_title')->nullable();
			$table->text('meta_keyword')->nullable();
			$table->text('meta_description')->nullable();
			$table->timestamps();
			
			$table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
