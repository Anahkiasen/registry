<?php
class MaintainersController extends BaseController
{

	/**
	 * Display all maintainers
	 *
	 * @return View
	 */
	public function index()
	{
		$maintainers = Maintainer::with('packages', 'packages.versions')->get();
		$maintainers = array_sort($maintainers, function($maintainer) {
			return $maintainer->popularity * -1;
		});

		return View::make('maintainers')
			->with('maintainers', $maintainers);
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
		$maintainer = Maintainer::with('packages', 'packages.versions')->whereSlug($slug)->firstOrFail();

		return View::make('maintainer')
			->with('maintainer', $maintainer);
	}

}
