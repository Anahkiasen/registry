<?php
namespace Registry\Services;

use Config;
use Guzzle\Http\Client as Guzzle;
use Illuminate\Support\Str;
use Registry\Repositories\MaintainersRepository;

/**
 * Authentification helpers for Maintainers
 */
class MaintainersAuth
{
	/**
	 * The Github OAuth endpoint
	 *
	 * @var Guzzle
	 */
	protected $github;

	/**
	 * The Github API endpoint
	 *
	 * @var Guzzle
	 */
	protected $api;

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
	 * Build a new MaintainersAuth service
	 *
	 * @param Guzzle $guzzle
	 */
	public function __construct(Guzzle $guzzle, MaintainersRepository $maintainers)
	{
		$guzzle->setDefaultOption('headers', ['Accept' => 'application/json']);

		$this->github      = clone $guzzle->setBaseUrl('https://github.com');
		$this->api         = clone $guzzle->setBaseUrl('https://api.github.com');
		$this->credentials = (object) Config::get('registry.api.github');
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
		$request = $this->github->post('/login/oauth/access_token')->addPostFields(array(
			'client_id'     => $this->credentials->id,
			'client_secret' => $this->credentials->secret,
			'code'          => $code,
		))->send()->json();

		// Cancel if invalid response
		if (!array_key_exists('access_token', $request)) {
			throw new Exception('An invalid response was received from Github');
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
		$user = $this->api->get('/user');
		$user->getQuery()->merge(array(
			'client_id'     => $this->credentials->id,
			'client_secret' => $this->credentials->secret,
			'access_token'  => $token,
		));
		$user = $user->send()->json();

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
		$maintainer = $this->maintainers->lookup($user['login']);
		if (!$maintainer) {
			$maintainer = $this->maintainers->create(array(
				'name'     => $user['login'],
				'slug'     => Str::slug($user['login']),
				'email'    => $user['email'],
				'github'   => $user['html_url'],
				'homepage' => $user['blog'],
			));
		}

		return $maintainer;
	}
}
