<?php
namespace Registry\PackagesEndpoints;

use Bitbucket\API\Repositories\Issues;
use Bitbucket\API\Repositories\Repository;
use Carbon\Carbon;

class BitbucketRepository extends AbstractRepository implements RepositoryInterface
{
	/**
	 * Get the Repository's opened issues
	 *
	 * @return integer
	 */
	public function openedIssues()
	{
		$closed = $this->issues('issues');
		if (!is_array($closed)) {
			$closed = 0;
		} else {
			$closed = array_filter($closed, function($issue) {
				return array_get($issue, 'status') === 'resolved';
			});
			$closed = sizeof($closed);
		}

		return $this->issues('count') - $closed;
	}

	/**
	 * Get the number of issues
	 *
	 * @return integer
	 */
	public function issuesCount()
	{
		return $this->issues('count');
	}

	/**
	 * Get the Repository's watchers
	 *
	 * @return integer
	 */
	public function favorites()
	{
		return $this->show('followers_count');
	}

	/**
	 * Get the Repository's forks
	 *
	 * @return integer
	 */
	public function forks()
	{
		return $this->show('forks_count');
	}

	/**
	 * Get the creation date of the Repository
	 *
	 * @return Carbon
	 */
	public function createdAt()
	{
		return new Carbon($this->show('utc_created_on'));
	}

	/**
	 * Get the last time the Repository was updated
	 *
	 * @return Carbon
	 */
	public function updatedAt()
	{
		return new Carbon($this->show('utc_last_updated'));
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ENDPOINTS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Repository's README
	 *
	 * @return string
	 */
	public function readmeEndpoint()
	{
		$readme = new Repository;

		return $readme->raw($this->package->vendor, $this->package->package, 'master', 'readme.md');
	}

	/**
	 * Get the issues
	 *
	 * @return array
	 */
	public function issuesEndpoint()
	{
		$informations = new Issues;
		$informations = $informations->all($this->package->vendor, $this->package->package);
		$informations = json_decode($informations, true);

		return $informations;
	}

	/**
	 * Get the core informations
	 *
	 * @return array
	 */
	public function showEndpoint()
	{
		$informations = $this->client->requestGet(sprintf('repositories/%s/%s', $this->package->vendor, $this->package->package));
		$informations = json_decode($informations, true);

		return $informations;
	}
}