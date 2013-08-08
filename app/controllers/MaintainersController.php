<?php
use Registry\Repositories\MaintainersRepository;
use Registry\Services\MaintainersAuth;

/**
 * Controller for Maintainers
 */
class MaintainersController extends BaseController
{
	/**
	 * The maintainers repository
	 *
	 * @var MaintainersRepository
	 */
	protected $maintainers;

	/**
	 * The MaintainersAuth instance
	 *
	 * @var MaintainersAuth
	 */
	protected $maintainersAuth;

	/**
	 * Build a new maintainersController
	 *
	 * @param MaintainersRepository $maintainers
	 * @param MaintainersAuth       $maintainersAuth
	 */
	public function __construct(MaintainersRepository $maintainers, MaintainersAuth $maintainersAuth)
	{
		$this->maintainers     = $maintainers;
		$this->maintainersAuth = $maintainersAuth;
	}

	/**
	 * Display all maintainers
	 *
	 * @return View
	 */
	public function index()
	{
		return View::make('maintainers', array(
			'maintainers' => $this->maintainers->popular(),
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
		return View::make('maintainer', array(
			'maintainer' => $this->maintainers->findBySlug($slug),
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
		$code  = Input::get('code');

		// Get token
		$token = $this->maintainersAuth->getAccessToken($code);
		if (!$token) {
			return Redirect::to('/')
				->with('errors', 'An error occured while accessing your Github account');
		}

		// Get Maintainer
		$user       = $this->maintainersAuth->getUserInformations($token);
		$maintainer = $this->maintainersAuth->getOrCreateMaintainer($user);

		// Log in Maintainer
		Auth::login($maintainer);

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
