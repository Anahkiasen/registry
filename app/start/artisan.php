<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

Rocketeer::after(array('deploy', 'update'), function($task) {
	$task->command->comment('Building assets');
	$task->runForCurrentRelease(['npm install', 'node node_modules/.bin/bower install', 'node node_modules/.bin/grunt production']);

	$task->command->comment('Uploading database and setting permissions');
	$task->remote->put(App::make('path').'/database/production.sqlite', $task->rocketeer->getFolder('shared/app/database/production.sqlite'));
	$task->setPermissions('app/database/production.sqlite');

	$task->command->comment('Clearing cache');
	$task->runForCurrentRelease('php artisan cache:clear && php artisan twig:clean');

	$task->runForCurrentRelease('chown -R www-data:www-data storage');
});

Artisan::add(new Refresh);
Artisan::add(new Update);
