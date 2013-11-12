<?php
namespace Registry\Repositories;

use Registry\Abstracts\AbstractRepository;
use Registry\Version;

/**
* The Versions Repository
*/
class VersionsRepository extends AbstractRepository
{
	/**
	 * Build a new Versions Repository
	 *
	 * @param Version $versions
	 */
	public function __construct(Version $versions)
	{
		$this->entries = $versions;
	}
}
