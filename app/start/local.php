<?php

 /////////////////////////////////////////////////////////////////////
 ////////////////////////////// DEPLOYMENT ///////////////////////////
 /////////////////////////////////////////////////////////////////////

Rocketeer::after('deploy', function($task) {
	$task->command->comment('Installing Bower components');
	$task->runForCurrentRelease('bower install');

	$task->command->comment('Building Basset containers');
	$task->runForCurrentRelease('php artisan basset:build -f -p');

	$task->setPermissions('app');
});