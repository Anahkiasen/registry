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
		$packages = Package::whereType($type)->get();
		$packages = array_sort($packages, function($package) {
			return -1 * $package->downloads;
		});

		return View::make('home')
			->with('packages', $packages);
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
		$package = Package::findOrFail($id);

		return View::make('package', compact('package'));
	}

}