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

	/**
	 * Search for a package
	 *
	 * @return Collection
	 */
	public function getSearch()
	{
		$query = Input::get('q');

		return Package::where('name', 'LIKE', '%' .$query. '%')->get();
	}

}