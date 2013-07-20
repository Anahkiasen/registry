<?php
use Illuminate\Database\Migrations\Migration;

class CreateVersions extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('versions', function($table) {
			$table->increments('id');
				$table->string('name');
				$table->string('description');
				$table->string('keywords');
				$table->string('homepage');
				$table->string('version');
			$table->integer('package_id');
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
		Schema::drop('versions');
	}

}
