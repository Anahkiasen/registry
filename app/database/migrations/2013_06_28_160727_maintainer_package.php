<?php
use Illuminate\Database\Migrations\Migration;

class MaintainerPackage extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintainer_package', function($table) {
			$table->increments('id');
				$table->integer('maintainer_id');
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
		Schema::drop('maintainer_package');
	}
}
