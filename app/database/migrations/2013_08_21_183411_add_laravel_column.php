<?php
use Illuminate\Database\Migrations\Migration;

class AddLaravelColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('packages', function ($table) {
			$table->string('laravel')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('packages', function ($table) {
			$table->dropColumn('laravel');
		});
	}
}
