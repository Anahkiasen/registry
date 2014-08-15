<?php
namespace Registry\Services;

use Github\Client as Github;
use Illuminate\Container\Container;
use Registry\Repositories\MaintainersRepository;

/**
 * Authentification helpers for Maintainers
 */
class MaintainersAuth
{
	/**
	 * The Github API credentials
	 *
	 * @var object
	 */
	protected $credentials;

	/**
	 * The Maintainers Repository
	 *
	 * @var MaintainersRepository
	 */
	protected $maintainers;

	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * Build a new MaintainersAuth service
	 *
	 * @param Container             $app
	 * @param MaintainersRepository $maintainers
	 */
	public function __construct(Container $app, MaintainersRepository $maintainers)
	{
		$this->app         = $app;
		$this->credentials = (object) $this->app['config']->get('services.github');
		$this->maintainers = $maintainers;
	}

	/**
	 * Get an access token from the Github OAuth
	 *
	 * @param  string $code
	 *
	 * @return string
	 */
	public function getAccessToken($code)
	{
		// Make and gather request
		$request = $this->app['endpoints.github']->post('/login/oauth/access_token');
		$request->addPostFields(array(
			'client_id'     => $this->credentials->id,
			'client_secret' => $this->credentials->secret,
			'code'          => $code,
		));
		$request = $request->send()->json();

		// Cancel if invalid response
		if (!array_key_exists('access_token', $request)) {
			return false;
		}

		return $request['access_token'];
	}

	/**
	 * Get the User informations
	 *
	 * @param  string $token
	 *
	 * @return object
	 */
	public function getUserInformations($token)
	{
		// Make and gather request
		$client = $this->app['endpoints.github_api'];
		$client->authenticate($token, null, Github::AUTH_URL_TOKEN);
		$user = $client->api('me')->show();

		return $user;
	}

	/**
	 * Get an existing maintainer matching an user
	 * Or create one on the fly
	 *
	 * @param  array  $user
	 *
	 * @return Maintainer
	 */
	public function getOrCreateMaintainer(array $user)
	{
		return $this->maintainers->findOrCreate(array(
			'name'     => array_get($user, 'login'),
			'email'    => array_get($user, 'email'),
			'github'   => array_get($user, 'html_url'),
			'homepage' => array_get($user, 'blog'),
		));
	}
}
