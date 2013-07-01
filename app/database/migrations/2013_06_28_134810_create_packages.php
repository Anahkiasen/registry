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

				// Package informations
				$table->string('name');
				$table->string('slug');
				$table->string('description');
				$table->integer('favorites');
				$table->string('type');
				$table->string('keywords');

				// Repository statistics
				$table->integer('downloads_total');
				$table->integer('downloads_monthly');
				$table->integer('downloads_daily');
				$table->integer('watchers');
				$table->integer('forks');
				$table->integer('seniority');
				$table->integer('freshness');

				// Indexes
				$table->integer('popularity');
				$table->integer('trust');

				// Endpoints
				$table->string('packagist');
				$table->string('repository');

			$table->timestamp('pushed_at');
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
