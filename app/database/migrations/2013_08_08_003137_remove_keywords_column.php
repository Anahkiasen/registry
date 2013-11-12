<?php
use Illuminate\Database\Migrations\Migration;

class RemoveKeywordsColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('packages', function ($table) {
			$table->dropColumn('keywords');
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
			$table->text('keywords')->nullable();
		});
	}
}
