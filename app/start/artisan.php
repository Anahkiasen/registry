<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////// DEPLOYMENT /////////////////////////////
//////////////////////////////////////////////////////////////////////

Rocketeer::after(array('deploy', 'update'), function($task) {
	$task->command->comment('Installing Bower components');
	$task->runForCurrentRelease('bower install');

	$task->command->comment('Building Basset containers');
	$task->runForCurrentRelease('php artisan basset:build -f -p');

	$task->command->info('Clearing cache');
	$task->runForCurrentRelease('php artisan cache:clear');
});

Artisan::add(new Refresh);
