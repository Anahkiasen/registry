<?php
class PackagesController extends BaseController
{

	/**
	 * Get all packages
	 *
	 * @param  string $type
	 *
	 * @return View
	 */
	public function index($type = 'package')
	{
		// Fetch packages, paginated
		$packages       = Package::with('maintainers', 'versions')->whereType($type)->orderBy('popularity', 'DESC')->get();
		$positionOffset = 1;

		return View::make('home', array(
			'packages'       => $packages,
			'positionOffset' => $positionOffset,
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
		$package = Package::with('maintainers', 'versions')->whereSlug($slug)->firstOrFail();

		// Get similar packages
		$similar = Package::where('name', '!=', $package->name)->where(function($query) use ($package) {
			foreach ($package->keywords as $keyword) {
				$query->orWhere('keywords', 'LIKE', '%"' .$keyword. '"%');
			}
		})->popular()->get();

		// Sort by number of tags in common
		$similar->sortBy(function($similarPackage) use($package) {
			return $similarPackage->popularity + sizeof(array_intersect($similarPackage->keywords, $package->keywords)) * -1;
		});

		return View::make('package')
			->with('similar', $similar)
			->with('package', $package);
	}

	/**
	 * Search for packages
	 *
	 * @return string
	 */
	public function search()
	{
		$query = Input::get('q');
		$html  = null;

		$packages = Package::whereType('package')
			->where('name', 'LIKE', "%$query%")
			->where('description', 'LIKE', "%$query%")
			->get();

		foreach ($packages as $key => $package) {
			$html .= View::make('partials.package', array('package' => $package, 'key' => $key, 'positionOffset' => 0));
		}

		return $html;
	}

}
