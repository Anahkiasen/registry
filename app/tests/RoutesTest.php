<?php

class RoutesTest extends Arrounded\Testing\RoutesTest
{
	/**
	 * The routes to ignore
	 *
	 * @var array
	 */
	protected $ignored = array(
		'_debugbar/open',
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
