<?php
use Illuminate\Database\Migrations\Migration;

class CreateComments extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function ($table) {
			$table->increments('id');
				$table->text('content');
				$table->integer('package_id');
				$table->integer('maintainer_id');
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
		Schema::drop('comments');
	}
}
