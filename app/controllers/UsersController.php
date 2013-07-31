<?php

class UsersController extends BaseController
{
	/**
	 * Log in the User
	 *
	 * @return View
	 */
	public function getLogin()
	{
		return View::make('users.login');
	}

	/**
	 * Validates credentials
	 *
	 * @return Redirect
	 */
	public function postLogin()
	{
		$credentials = Input::only('login', 'password');
		if (!Auth::attempt($credentials)) {
			return Redirect::back()->withInput()->with('error', true);
		}

		return Redirect::to('/');
	}

	/**
	 * Register to the registry
	 *
	 * @return View
	 */
	public function getRegister()
	{
		return View::make('users.register');
	}

	/**
	 * Validate register
	 *
	 * @return Redirect
	 */
	public function postRegister()
	{
		$credentials = Input::all();
		$validation  = Validator::make($credentials, User::$rules);

		if ($validation->fails()) {
			return Redirect::action('UsersController@getRegister')
				->withInput()->withErrors($validation);
		}

		User::create($credentials);

		return Redirect::action('UsersController@getRegister')
			->with('success', true);
	}
}