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
				$table->string('versionNormalized');
				$table->string('license');
				$table->string('authors');
				$table->string('source');
				$table->string('dist');
				$table->string('autoload');
				$table->string('extra');
				$table->string('require');
				$table->string('requireDev');
				$table->string('bin');

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