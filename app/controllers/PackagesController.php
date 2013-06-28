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
	public function getIndex($type = 'package')
	{
		$packages = Package::with('maintainers', 'versions')->whereType($type)->get();
		$packages = array_sort($packages, function($package) {
			return -1 * $package->downloads;
		});

		return View::make('home', array(
			'packages' => array_values($packages),
		));
	}

	/**
	 * Display a package
	 *
	 * @param  integer $id
	 *
	 * @return View
	 */
	public function getPackage($id)
	{
		$package = Package::with('maintainers', 'versions')->findOrFail($id);

		return View::make('package', compact('package'));
	}

}
