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
		$packages       = Package::with('maintainers', 'versions')->whereType($type)->get();
		// $positionOffset = 1 + ($packages->getPerPage() * ($packages->getCurrentPage() - 1));
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
	public function package($id)
	{
		$package = Package::with('maintainers', 'versions')->findOrFail($id);

		return View::make('package', compact('package'));
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
