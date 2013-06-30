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

Route::get('/',                 'PackagesController@index');

// Maintainers
Route::get('maintainers',       'MaintainersController@index');
Route::get('maintainer/{slug}', 'MaintainersController@maintainer');

// Packages
Route::get('{type?}',           'PackagesController@index');
Route::get('package/{package}', 'PackagesController@package');