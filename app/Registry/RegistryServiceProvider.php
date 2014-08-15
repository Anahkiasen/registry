<?php
namespace Registry;

use Arrounded\Abstracts\AbstractServiceProvider;
use Auth;
use Config;

class RegistryServiceProvider extends AbstractServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['view']->composer('home', function ($view) {
			$view->columns = Auth::check() ? Auth::user()->columns : Config::get('registry.columns');
		});
	}
}
