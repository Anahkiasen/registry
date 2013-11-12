<?php
namespace Registry\Abstracts;

use Registry\Repositories\MaintainersRepository;
use Registry\Repositories\PackagesRepository;
use Registry\Repositories\VersionsRepository;

/**
 * A core seeder
 */
abstract class AbstractSeeder extends \Arrounded\Seeders\AbstractSeeder
{
	/**
	 * The Maintainers Repository
	 *
	 * @var MaintainersRepository
	 */
	protected $maintainers;

	/**
	 * The Versions Repository
	 *
	 * @var VersionsRepository
	 */
	protected $versions;

	/**
	 * The Packages Repository
	 *
	 * @var PackagesRepository
	 */
	protected $packages;

	/**
	 * Build the seed
	 *
	 * @param PackagesRepository    $packages
	 * @param VersionsRepository    $versions
	 * @param MaintainersRepository $maintainers
	 */
	public function __construct(PackagesRepository $packages, VersionsRepository $versions, MaintainersRepository $maintainers)
	{
		parent::__construct();

		$this->packages    = $packages;
		$this->versions    = $versions;
		$this->maintainers = $maintainers;
	}
}
