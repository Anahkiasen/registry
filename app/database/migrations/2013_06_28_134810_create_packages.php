<?php
use Illuminate\Database\Migrations\Migration;

class CreatePackages extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('packages', function($table) {
			$table->increments('id');
				$table->string('name');
				$table->string('description');
				$table->integer('favorites');
				$table->integer('downloads');
				$table->string('type');
				$table->string('tags');

				$table->string('packagist');
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
		Schema::drop('packages');
	}

}