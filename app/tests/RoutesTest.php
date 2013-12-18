<?php

class RoutesTest extends Arrounded\Testing\RoutesTest
{
	/**
	 * The routes to ignore
	 *
	 * @var array
	 */
	protected $ignored = array(
		'_profiler',
		'maintainers/confirm',
		'maintainers/logout',
	);

	/**
	 * The model namespace
	 *
	 * @var string
	 */
	protected $namespace = 'Registry\\';
}
