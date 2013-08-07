<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

// Common routes
//////////////////////////////////////////////////////////////////////

Route::get('about', function() {
	return View::make('about');
});

// Packages
//////////////////////////////////////////////////////////////////////

Route::get('/',                  'PackagesController@index');
Route::get('packages',           'PackagesController@index');
Route::get('packages/history',   'PackagesController@history');
Route::get('package/{package}',  ['as' => 'package', 'uses' => 'PackagesController@package']);
Route::post('package/{package}', 'PackagesController@comment');

// Maintainers
//////////////////////////////////////////////////////////////////////

Route::get('maintainers',             'MaintainersController@index');
Route::get('maintainer/{maintainer}', ['as' => 'maintainer', 'uses' => 'MaintainersController@maintainer']);
Route::get('maintainers/confirm',     'MaintainersController@confirm');
Route::get('maintainers/logout',      'MaintainersController@logout');

// API
//////////////////////////////////////////////////////////////////////

Route::group(array('prefix' => 'api'), function () {
	Route::get('packages',           'Api\PackagesController@index');
	Route::get('packages/latest',    'Api\PackagesController@latest');
	Route::get('packages/popular',   'Api\PackagesController@popular');
	Route::get('packages/{package}', 'Api\PackagesController@package');

	Route::get('maintainers',                       'Api\MaintainersController@index');
	Route::get('maintainers/{maintainer}',          'Api\MaintainersController@maintainer');
	Route::get('maintainers/{maintainer}/packages', 'Api\MaintainersController@packages');
});

// Error routes
//////////////////////////////////////////////////////////////////////

App::error(function(ModelNotFoundException $exception) {
	return View::make('errors.404');
});
