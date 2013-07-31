<?php
use Guzzle\Http\Client as Guzzle;

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
	 * Confirm OAuth code
	 *
	 * @return Redirect
	 */
	public function confirm()
	{
		$code   = Input::get('code');
		$github = Config::get('registry.api.github');
		$guzzle = new Guzzle;
		$guzzle->setDefaultOption('headers', ['Accept' => 'application/json']);

		// Get token
		$request = clone $guzzle->setBaseUrl('https://github.com');
		$request = $request->post('/login/oauth/access_token')->addPostFields(array(
			'client_id'     => $github['id'],
			'client_secret' => $github['secret'],
			'code'          => $code,
		));
		$request = $request->send()->json();

		// Cancel if error
		if (!isset($request['access_token'])) {
			return Redirect::to('/');
		}

		// Get User informations
		$api = clone $guzzle->setBaseUrl('https://api.github.com');
		$user = $api->get('/user');
		$user->getQuery()->merge(array(
			'client_id'     => $github['id'],
			'client_secret' => $github['secret'],
			'access_token'  => $request['access_token'],
		));
		$user = $user->send()->json();

		// Get existing User
		$user = Maintainer::whereName($user['login'])->first() ?: Maintainer::create([
			'name'     => $user['login'],
			'slug'     => Str::slug($user['login']),
			'email'    => $user['email'],
			'github'   => $user['html_url'],
			'homepage' => $user['blog'],
		]);

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
