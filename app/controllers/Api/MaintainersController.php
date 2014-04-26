<?php
namespace Api;

use Registry\Abstracts\AbstractApiController;
use Registry\Repositories\MaintainersRepository;

class MaintainersController extends AbstractApiController
{
	/**
	 * Build a new maintainersController
	 *
	 * @param MaintainersRepository $maintainers
	 */
	public function __construct(MaintainersRepository $maintainers)
	{
		$this->repository = $maintainers;
	}

	/**
	 * Get all Maintainers
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->repository->all();
	}

	/**
	 * Get the most popular maintainers
	 *
	 * @return Collection
	 */
	public function popular()
	{
		return $this->repository->popular();
	}

	/**
	 * Get a Maintainer's informations
	 *
	 * @param  integer $maintainer
	 *
	 * @return Maintainer
	 */
	public function maintainer($maintainer)
	{
		return $this->repository->find($maintainer);
	}

	/**
	 * Get the packages of a Maintainer
	 *
	 * @param  integer $maintainer
	 *
	 * @return Collection
	 */
	public function packages($maintainer)
	{
		return $this->repository->find($maintainer)->packages;
	}
}
