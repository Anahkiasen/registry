<?php
use Illuminate\Database\Migrations\Migration;

class AddReadmeColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('packages', function ($table) {
			$table->text('readme');
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
			$table->dropColumn('readme');
		});
	}
}
