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

Route::get('/', 'PackagesController@index');

Route::get('search',  'PackagesController@search');
Route::get('{type?}', 'PackagesController@index');

Route::get('package/{package}', 'PackagesController@package')
	->where('package', '[0-9]{1,3}');