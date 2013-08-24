<?php
use Illuminate\Console\Command;

/**
 * Updates the database and deploy an new version of the Registry
 */
class Update extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'registry:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates the Registry';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Refresh database
		$this->call('registry:refresh');

		// Commit and deploy
		exec('git push origin master');
		$this->call('deploy:deploy');
	}
}
