<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

Rocketeer::after(array('deploy', 'update'), function($task) {
	$task->command->comment('Installing Bower components');
	$task->runForCurrentRelease('bower install');

	$task->command->comment('Building Basset containers');
	$task->runForCurrentRelease('php artisan basset:build -f -p');

	$task->setPermissions('app');
});

Rocketeer::after('update', function($task) {
	$task->command->info('Updating database and clearing cache');
	$task->remote->put(App::make('path').'/database/production.sqlite', $task->rocketeer->getFolder('shared/app/database/production.sqlite'));
	$task->runForCurrentRelease('php artisan cache:clear');
});

Artisan::add(new Refresh);