<?php
use Registry\Repositories\PackagesRepository;
use Registry\Services\CommentServices;

/**
 * Controller for Packages
 */
class PackagesController extends BaseController
{
	/**
	 * The Packages repository
	 *
	 * @var PackagesRepository
	 */
	protected $packages;

	/**
	 * The Comments services
	 *
	 * @var CommentServices
	 */
	protected $comments;

	/**
	 * Build a new PackagesController
	 *
	 * @param PackagesRepository $packages The Packages Repository
	 * @param CommentServices    $comments
	 */
	public function __construct(PackagesRepository $packages, CommentServices $comments)
	{
		$this->packages = $packages;
		$this->comments = $comments;
	}

	/**
	 * Get all packages
	 *
	 * @param  string $type
	 *
	 * @return View
	 */
	public function index()
	{
		return View::make('home', array(
			'packages' => $this->packages->all(),
		));
	}

	/**
	 * Display a package
	 *
	 * @param  string $slug
	 *
	 * @return View
	 */
	public function package($slug)
	{
		$package = $this->packages->findBySlug($slug);

		return View::make('package', array(
			'package' => $package,
			'similar' => $this->packages->findSimilarTo($package),
		));
	}

	/**
	 * Comment a package
	 *
	 * @param  string $slug
	 *
	 * @return Redirect
	 */
	public function comment($slug)
	{
		$input      = Input::only('content');
		$validation = $this->comments->createFromInput($input, $slug);
		if (!$validation) {
			return Redirect::back()->withInput()->withErrors($validation);
		}

		return Redirect::action('PackagesController@package', $slug);
	}

	/**
	 * Get the history of packages
	 *
	 * @return Response
	 */
	public function history()
	{
		$packages = $this->packages->oldest();
		$history  = array();
		foreach ($packages as $key => $package) {
			$date           = $package->created_at->format('Y-m');
			$dates[$date]   = $date;
			$history[$date] = $key;
		}

		// Sort
		ksort($dates);
		ksort($history);

		return array(
			'labels' => array_values($dates),
			'data'   => array_values($history),
		);
	}
}
