<?php

/**
 * Controller for Maintainers
 */
class MaintainersController extends BaseController
{
	/**
	 * Display all maintainers
	 *
	 * @return View
	 */
	public function index()
	{
		$maintainers = Maintainer::with('packages.versions')->get();
		$maintainers = array_sort($maintainers, function($maintainer) {
			return $maintainer->popularity * -1;
		});

		return View::make('maintainers', array(
			'maintainers' => $maintainers,
		));
	}

	/**
	 * Display a Maintainer
	 *
	 * @param  string $slug
	 *
	 * @return View
	 */
	public function maintainer($slug)
	{
		$maintainer = Maintainer::with('packages.versions')->whereSlug($slug)->firstOrFail();

		return View::make('maintainer', array(
			'maintainer' => $maintainer,
		));
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// AUTHENTIFICATION ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Log in via Github
	 *
	 * @return Redirect
	 */
	public function authorize()
	{
		return Redirect::to('https://github.com/login/oauth/authorize?client_id='.Config::get('registry.api.github.id'));
	}

	/**
	 * Confirm OAuth code
	 *
	 * @return Redirect
	 */
	public function confirm()
	{
		$code   = Input::get('code');
		$github = Config::get('registry.api.github');

		// Get token
		$request = new Guzzle('https://github.com');
		$request->setDefaultOption('headers', array('Accept' => 'application/json'));
		$request = $request->post('/login/oauth/access_token');
		$request->setPostField('client_id', $github['id']);
		$request->setPostField('client_secret', $github['secret']);
		$request->setPostField('code', $code);
		$request = $request->send()->json();

		if (!isset($request['access_token'])) {
			return Redirect::to('/');
		}

		// Get User informations
		$api = new Guzzle('https://api.github.com');
		$api->setDefaultOption('headers', array('Accept' => 'application/json'));
		$user = $api->get('/user');
		$user->getQuery()->set('client_id', $github['id']);
		$user->getQuery()->set('client_secret', $github['secret']);
		$user->getQuery()->set('access_token', $request['access_token']);
		$user = $user->send()->json();

		// Get existing User
		$existing = Maintainer::whereName($user['login'])->first();
		if (!$existing) {
			$user = Maintainer::create([
				'name'     => $user['login'],
				'slug'     => Str::slug($user['login']),
				'email'    => $user['email'],
				'github'   => $user['html_url'],
				'homepage' => $user['blog'],
			]);
		} else {
			$user = $existing;
		}

		// Log in user
		Auth::login($user, true);

		return Redirect::to('/');
	}

	/**
	 * Logout the current User
	 *
	 * @return Redirect
	 */
	public function logout()
	{
		Auth::logout();

		return Redirect::to('/');
	}
}
