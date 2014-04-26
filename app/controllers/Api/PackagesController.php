<?php
namespace Api;

use Registry\Abstracts\AbstractApiController;
use Registry\Repositories\PackagesRepository;

class PackagesController extends AbstractApiController
{
	/**
	 * Build a new PackagesController
	 *
	 * @param PackagesRepository $packages The Packages Repository
	 */
	public function __construct(PackagesRepository $packages)
	{
		$this->repository = $packages;
	}

	/**
	 * Get all packages
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->repository->all();
	}

	/**
	 * Get the latest packages
	 *
	 * @return Collection
	 */
	public function latest()
	{
		return $this->repository->latest();
	}

	/**
	 * Get the most popular packages first
	 *
	 * @return Collection
	 */
	public function popular()
	{
		return $this->repository->popular();
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
		return $this->repository->find($package);
	}
}
