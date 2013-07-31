<?php
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
			$table->increments('id');
				$table->string('username');
				$table->string('password');
				$table->string('email');
				$table->string('activation_code');
				$table->boolean('activated')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}
}
