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
		$packages = Package::with('maintainers')->whereType('package')->orderBy('popularity', 'DESC')->get();

		return View::make('home', array(
			'packages' => $packages,
		));
	}

	/**
	 * Display a package
	 *
	 * @param  integer $id
	 *
	 * @return View
	 */
	public function package($slug)
	{
		// Get packages and similar packages
		$package = Package::with('versions')->whereSlug($slug)->firstOrFail();
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
	 * Get the history of packages
	 *
	 * @return Response
	 */
	public function history()
	{
		$packages = Package::orderBy('created_at', 'asc')->get();
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
