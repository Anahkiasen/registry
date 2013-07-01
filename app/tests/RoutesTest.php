<?php
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoutesTest extends TestCase
{

	public function testCanDisplayAllRoutes()
	{
		// Create basic routes array
		$routes = array(
			URL::action('PackagesController@index'),
			URL::action('MaintainersController@index'),
		);

		// Add resources routes
		foreach (Maintainer::all() as $maintainer) {
			$routes[] = URL::action('MaintainersController@maintainer', $maintainer->slug);
		}
		foreach (Package::all() as $package) {
			$routes[] = URL::action('PackagesController@package', $package->slug);
		}

		// Test all routes
		foreach ($routes as $route) {
			try {
				print 'Testing route '.$route.PHP_EOL;

				$response = $this->call('GET', $route);
				$this->assertResponseOk();
			} catch (NotFoundHttpException $exception) {
				$this->fail('Route "' .$route. '" was not found');
			}
		}
	}

}
