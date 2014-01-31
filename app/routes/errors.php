<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

// Error routes
//////////////////////////////////////////////////////////////////////

App::error(function (ModelNotFoundException $exception) {
	return View::make('errors.404');
});
