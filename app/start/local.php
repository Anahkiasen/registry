<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

Rocketeer::after(array('deploy', 'update'), function ($task) {
	$task->command->comment('Building assets');
	$task->runForCurrentRelease(['npm install', 'node node_modules/.bin/bower install --allow-root', 'node node_modules/.bin/grunt production']);

	$task->command->comment('Clearing cache');
	$task->runForCurrentRelease('php artisan cache:clear && php artisan twig:clean');

	$task->command->comment('Uploading database credentials');
	$task->remote->put(App::make('path').'/config/production/database.php', $task->rocketeer->getFolder('current/config/production/database.php'));

	$task->runForCurrentRelease('chown -R www-data:www-data storage');
});
