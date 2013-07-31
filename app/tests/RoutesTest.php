<?php

class RoutesTest extends TestCase
{
	/**
	 * The routes to test
	 *
	 * @var array
	 */
	protected $routes = array();

	/**
	 * The routes that failed
	 *
	 * @var array
	 */
	protected $failed = array();

	/**
	 * Test all routes and actions
	 *
	 * @return void
	 */
	public function testCanDisplayPages()
	{
		// Add manual routes
		$this->routes = array(
			URL::action('PackagesController@index'),
			URL::action('MaintainersController@index'),
		);

		// Add resources routes
		foreach (Maintainer::all() as $maintainer) {
			$this->routes[] = URL::route('maintainer', $maintainer->slug);
		}
		foreach (Package::all() as $package) {
			$this->routes[] = URL::route('package', $package->slug);
		}

		foreach ($this->routes as $route) {
			$shorthand = str_replace(Request::root(), null, $route);

			try {
				$this->comment('Testing route '.$shorthand);
				$this->call('GET', $route);
				$this->assertResponseOk();
			} catch (Exception $exception) {
				$this->failedRoute($shorthand, $exception->getMessage());
			}
		}

		// Print summary
		print PHP_EOL.str_repeat('-', 75).PHP_EOL;
		$this->success(sizeof($this->routes). ' route(s) were tested');
		if (!empty($this->failed)) {
			$this->info(sizeof($this->failed). ' problem(s) were encountered :');
			foreach ($this->failed as $route => $message) {
				$this->error($route.str_repeat(' ', 25 - strlen($route)).$message);
			}

			$this->fail();
		}
	}

	/**
	 * Fail a route
	 *
	 * @param  string $route
	 * @param  string $message
	 *
	 * @return void
	 */
	protected function failedRoute($route, $message)
	{
		$this->failed[$route] = sprintf($message, $route);
	}
}


