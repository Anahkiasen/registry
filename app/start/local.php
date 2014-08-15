<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

RiRocketeer::after(array('deploy', 'update'), function ($task) {
	$task->command->comment('Building assets');
	$task->runForCurrentRelease(['node_modules/.bin/grunt production']);

	$task->command->comment('Clearing cache');
	$task->runForCurrentRelease('php artisan cache:clear && php artisan twig:clear');

	$task->command->comment('Uploading database credentials');
	$task->remote->put(App::make('path').'/config/production/database.php', $task->rocketeer->getFolder('current/config/production/database.php'));
});
