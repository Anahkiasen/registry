<?php

// Common routes
//////////////////////////////////////////////////////////////////////

Route::get('about', function () {
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
