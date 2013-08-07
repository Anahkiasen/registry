<?php
namespace Api;

use BaseController;
use Registry\Repositories\MaintainersRepository;

class MaintainersController extends BaseController
{
	/**
	 * The maintainers repository
	 *
	 * @var MaintainersRepository
	 */
	protected $maintainers;

	/**
	 * Build a new maintainersController
	 *
	 * @param MaintainersRepository $maintainers
	 */
	public function __construct(MaintainersRepository $maintainers)
	{
		$this->maintainers = $maintainers;
	}

	/**
	 * Get all Maintainers
	 *
	 * @return Collection
	 */
	public function index()
	{
		return $this->maintainers->all();
	}

	/**
	 * Get the most popular maintainers
	 *
	 * @return Collection
	 */
	public function popular()
	{
		return $this->maintainers->popular();
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
		return $this->maintainers->find($maintainer);
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
		return $this->maintainers->find($maintainer)->packages;
	}
}
