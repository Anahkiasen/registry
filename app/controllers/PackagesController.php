<?php

/**
 * Controller for Packages
 */
class PackagesController extends BaseController
{
	/**
	 * Get all packages
	 *
	 * @param  string $type
	 *
	 * @return View
	 */
	public function index()
	{
		// Fetch packages, paginated
		$packages = Package::with('maintainers')->whereType('package')->latest('popularity')->get();

		return View::make('home', array(
			'packages' => $packages,
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
		// Get packages and similar packages
		$package = Package::with('versions', 'comments.maintainer')->whereSlug($slug)->firstOrFail();
		$similar = Package::with('versions')->similar($package)->take(5)->get();

		// Sort by popularity and number of tags in common
		$similar->sortBy(function($similarPackage) use ($package) {
			return $similarPackage->popularity + sizeof(array_intersect($similarPackage->keywords, $package->keywords)) * -1;
		});

		return View::make('package', array(
			'similar' => $similar,
			'package' => $package,
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
		$validation = Validator::make($input, ['content' => 'required']);
		if ($validation->fails()) {
			return Redirect::back()->withInput()->withErrors($validation);
		}

		// Create comment
		Comment::create(array(
			'content'       => $input['content'],
			'maintainer_id' => Auth::user()->id,
			'package_id'    => Package::whereSlug($slug)->firstOrFail()->id,
		));

		return Redirect::action('PackagesController@package', $slug);
	}

	/**
	 * Get the history of packages
	 *
	 * @return Response
	 */
	public function history()
	{
		$packages = Package::oldest()->get();
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
