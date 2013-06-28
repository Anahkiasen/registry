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
		$packages = Package::orderBy('downloads', 'DESC')->whereType($type)->get();

		return View::make('home')
			->with('packages', $packages);
	}

}