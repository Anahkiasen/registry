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
		Schema::create('packages', function ($table) {
			$table->increments('id');

				// Package informations
				$table->string('name');
				$table->string('slug')->nullable();
				$table->text('description');
				$table->integer('favorites')->default(0);
				$table->string('type');
				$table->string('keywords');
				$table->boolean('illuminate')->default(false);

				// Unit tests
				$table->integer('build_status')->default(0);
				$table->integer('consistency')->default(0);
				$table->integer('coverage')->default(0);

				// Repository statistics
				$table->integer('downloads_total')->default(0);
				$table->integer('downloads_monthly')->default(0);
				$table->integer('downloads_daily')->default(0);
				$table->integer('watchers')->default(0);
				$table->integer('forks')->default(0);
				$table->integer('issues')->default(0);
				$table->integer('seniority')->default(0);
				$table->integer('freshness')->default(0);

				// Indexes
				$table->integer('popularity')->default(0);
				$table->integer('trust')->default(0);

				// Endpoints
				$table->string('travis')->nullable();
				$table->string('packagist')->nullable();
				$table->string('repository')->nullable();

			$table->timestamp('pushed_at')->nullable();
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
