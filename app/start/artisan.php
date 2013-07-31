<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

Rocketeer::after(array('deploy', 'update'), function($task) {
	$task->command->comment('Installing Bower components');
	$task->runForCurrentRelease('bower install --allow-root');

	$task->command->comment('Building Basset containers');
	$task->runForCurrentRelease('php artisan basset:build -f -p');

	// $task->command->comment('Updating database');
	// $task->remote->put(App::make('path').'/database/production.sqlite', $task->rocketeer->getFolder('shared/app/database/production.sqlite'));

	$task->command->comment('Clearing cache');
	$task->runForCurrentRelease('php artisan cache:clear && php artisan twig:clean');

	$task->runForCurrentRelease('chown www-data app/storage/meta/collections.json');
});

Artisan::add(new Refresh);
