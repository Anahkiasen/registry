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
}