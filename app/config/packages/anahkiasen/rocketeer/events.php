<?php
use Rocketeer\Facades\Rocketeer;

Rocketeer::addTaskListeners('deploy', 'before-symlink', array(
	'grunt',
	'cache',
	'credentials',
));
