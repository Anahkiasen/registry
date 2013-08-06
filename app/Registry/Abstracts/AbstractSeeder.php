<?php
namespace Registry\Abstracts;

use Illuminate\Database\Seeder;
use Registry\Repositories\MaintainersRepository;
use Registry\Repositories\PackagesRepository;
use Registry\Repositories\VersionsRepository;
use Registry\Traits\Colorizer;

/**
 * A core seeder
 */
abstract class AbstractSeeder extends Seeder
{
	use Colorizer;

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
	 * @param PackagesRepository $packages
	 */
	public function __construct(PackagesRepository $packages, VersionsRepository $versions, MaintainersRepository $maintainers)
	{
		$this->packages    = $packages;
		$this->versions    = $versions;
		$this->maintainers = $maintainers;
	}
}
