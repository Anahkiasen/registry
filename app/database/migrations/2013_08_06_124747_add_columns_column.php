<?php
use Illuminate\Database\Migrations\Migration;

class AddColumnsColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('maintainers', function ($table) {
			$table->text('columns')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('maintainers', function ($table) {
			$table->dropColumn('columns');
		});
	}
}
