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

Route::get('/', 'PackagesController@getIndex');

Route::get('{type?}', 'PackagesController@getIndex');

Route::get('/package/{package}', 'PackagesController@getPackage')
	->where('package', '[0-9]{1,3}');