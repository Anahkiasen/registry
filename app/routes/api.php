<?php

// Packages
////////////////////////////////////////////////////////////////////

Route::get('packages',           'Api\PackagesController@index');
Route::get('packages/latest',    'Api\PackagesController@latest');
Route::get('packages/popular',   'Api\PackagesController@popular');
Route::get('packages/{package}', 'Api\PackagesController@package');

// Maintainers
//////////////////////////////////////////////////////////////////////

Route::get('maintainers',                       'Api\MaintainersController@index');
Route::get('maintainers/{maintainer}',          'Api\MaintainersController@maintainer');
Route::get('maintainers/{maintainer}/packages', 'Api\MaintainersController@packages');

// Error routes
//////////////////////////////////////////////////////////////////////

App::error(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
	return Response::json(array(
		'status'  => 404,
		'message' => 'The package or maintainer requested could not be found',
	), 404);
});
