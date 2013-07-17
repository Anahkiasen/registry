<?php

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
Route::get('/',                 'PackagesController@index');
Route::get('packages',          'PackagesController@index');
Route::get('package/{package}', 'PackagesController@package');

// Maintainers
Route::get('maintainers',       'MaintainersController@index');
Route::get('maintainer/{slug}', 'MaintainersController@maintainer');
