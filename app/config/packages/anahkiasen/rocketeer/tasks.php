<?php
use Rocketeer\Abstracts\AbstractTask;
use Rocketeer\Facades\Rocketeer;

Rocketeer::task('grunt', 'node_modules/.bin/grunt production');

Rocketeer::task('cache', array(
	'php artisan cache:clear',
	'php artisan twig:clear',
));

Rocketeer::task('credentials', function (AbstractTask $task) {
	return $task->upload(
		app_path('config/production/database.php'),
		$task->releasesManager->getCurrentReleasePath('app/config/production/database.php')
	);
});
