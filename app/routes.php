<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Common routes
Route::get('about', function() {
	return View::make('about');
});

// Packages
Route::get('/',                  'PackagesController@index');
Route::get('packages',           'PackagesController@index');
Route::get('packages/history',   'PackagesController@history');
Route::get('package/{package}',  ['as' => 'package', 'uses' => 'PackagesController@package']);
Route::post('package/{package}', 'PackagesController@comment');

// Maintainers
Route::get('maintainers',       'MaintainersController@index');
Route::get('maintainer/{slug}', ['as' => 'maintainer', 'uses' => 'MaintainersController@maintainer']);
Route::get('maintainers/confirm', 'MaintainersController@confirm');
Route::get('maintainers/logout',  'MaintainersController@logout');

App::error(function(ModelNotFoundException $exception) {
	return View::make('errors.404');
});