<?php
use Illuminate\Database\Migrations\Migration;

class CreateMaintainers extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintainers', function ($table) {
			$table->increments('id');
				$table->string('name');
				$table->string('slug');
				$table->string('email');
				$table->string('github')->nullable();
				$table->string('homepage')->nullable();
				$table->text('stars')->nullable();
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
		Schema::drop('maintainers');
	}
}
