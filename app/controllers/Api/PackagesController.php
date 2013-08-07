<?php
namespace Api;

use BaseController;
use Registry\Repositories\PackagesRepository;

class PackagesController extends BaseController
{
	/**
	 * The Packages repository
	 *
	 * @var PackagesRepository
	 */
	protected $packages;

	/**
	 * Build a new PackagesController
	 *
	 * @param PackagesRepository $packages The Packages Repository
	 */
	public function __construct(PackagesRepository $packages)
	{
		$this->packages = $packages;
	}

	/**
	 * Get all packages
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->packages->all();
	}

	/**
	 * Get the latest packages
	 *
	 * @return Collection
	 */
	public function latest()
	{
		return $this->packages->latest();
	}

	/**
	 * Get the most popular packages first
	 *
	 * @return Collection
	 */
	public function popular()
	{
		return $this->packages->popular();
	}

	/**
	 * Find a package
	 *
	 * @param  integer $package
	 *
	 * @return Package
	 */
	public function package($package)
	{
		return $this->packages->find($package);
	}
}
