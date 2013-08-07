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
	 * Get the routes to test
	 *
	 * @return array
	 */
	protected function getRoutes()
	{
		$routes = array();

		foreach (Route::getRoutes() as $route) {
			$method = $route->getMethods()[0];
			$uri    = $route->getPath();

			// Skip some routes
			if ($method != 'GET' or Str::contains($uri, $this->ignored)) {
				continue;
			}

			// Replace model with their IDs
			preg_match('/\{([^}]+)\}/', $uri, $pattern);
			$model = Str::studly(array_get($pattern, 1));
			$model = 'Registry\\'.Str::singular($model);
			if (class_exists($model)) {

				foreach ($model::all() as $model) {
					$attribute = Str::contains($uri, 'api') ? 'id' : 'slug';
					$model     = str_replace('{'.$pattern[1].'}', $model->$attribute, $uri);
					$routes[]  = URL::to($model);
				}
				continue;
			}

			$routes[] = URL::to($uri);
		}

		return $routes;
	}

	/**
	 * Test all routes and actions
	 *
	 * @return void
	 */
	public function testCanDisplayPages()
	{
		$this->routes = $this->getRoutes();

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
		print PHP_EOL.str_repeat('-', $this->padding()).PHP_EOL;
		$this->success(sizeof($this->routes). ' route(s) were tested');
		if (!empty($this->failed)) {
			$this->info(sizeof($this->failed). ' problem(s) were encountered :');
			foreach ($this->failed as $route => $message) {
				$this->error($route.str_repeat(' ', $this->padding() - strlen($route)).$message);
			}

			$this->fail();
		}
	}

	/**
	 * Get the length of the longest URL to display
	 *
	 * @return integer
	 */
	protected function padding()
	{
		$routes = $this->routes;
		$routes = array_sort($routes, function($route) {
			return strlen($route) * -1;
		});

		return strlen($routes[key($routes)]);
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
