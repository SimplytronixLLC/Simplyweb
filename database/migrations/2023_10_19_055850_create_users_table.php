<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */ 
    
	public function up() {
		Schema::create('users', function (Blueprint $table) {
			$table->id(); // Auto-increment primary key
			$table->string('name');
			$table->string('username');
			$table->string('user_type');
			$table->string('login_type');
			$table->string('status');
			$table->string('email')->unique(); // Unique constraint for email
			$table->timestamp('email_verified_at')->nullable(); // Nullable email verification timestamp
			$table->string('email_verify');
			$table->string('password');
			$table->string('phone');
			$table->string('photo');
			$table->rememberToken(); // Remember me token
			$table->timestamps(); // Created at and updated at timestamps
		});
	}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
