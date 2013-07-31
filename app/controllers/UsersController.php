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
		$credentials = Input::only('username', 'password');
		if (!Auth::attempt($credentials)) {
			return Redirect::back()->withInput()->with('error', true);
		}

		if (!Auth::user()->activated) {
			Auth::logout();
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

		// Create User
		$credentials['activation_code'] = Str::random();
		User::create($credentials);

		// Send code
		Mail::send('emails.register', $credentials, function($mail) use ($credentials) {
			$mail->to($credentials['email']);
		});

		return Redirect::action('UsersController@getRegister')
			->with('success', true);
	}

	/**
	 * Activate an User
	 *
	 * @param  string $code
	 *
	 * @return View
	 */
	public function getActivate($code)
	{
		$user = User::whereActivationCode($code)->firstOrFail();
		$user->activation_code = null;
		$user->activated = true;
		$user->save();

		return View::make('users.activate');
	}
}