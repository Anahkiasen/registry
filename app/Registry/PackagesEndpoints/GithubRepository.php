<?php
namespace Registry\PackagesEndpoints;

use Carbon\Carbon;
use Exception;

class GithubRepository extends AbstractRepository implements RepositoryInterface
{
	/**
	 * Get the Repository's opened issues
	 *
	 * @return integer
	 */
	public function openedIssues()
	{
		return $this->show('open_issues_count');
	}

	/**
	 * Get the number of issues
	 *
	 * @return integer
	 */
	public function issuesCount()
	{
		list($opened, $closed) = $this->issues();

		return $opened > $closed ? $opened : $closed;
	}

	/**
	 * Get the Repository's watchers
	 *
	 * @return integer
	 */
	public function favorites()
	{
		return $this->show('watchers_count');
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
		return new Carbon($this->show('created_at'));
	}

	/**
	 * Get the last time the Repository was updated
	 *
	 * @return Carbon
	 */
	public function updatedAt()
	{
		return new Carbon($this->show('pushed_at'));
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
		$file = $this->client->api('repos')->contents()->readme($this->package->vendor, $this->package->package);
		$file = $file['name'];

		return $this->client->api('repos')->contents()->download($this->package->vendor, $this->package->package, $file);
	}

	/**
	 * Get the open and closed issues
	 *
	 * @return array
	 */
	public function issuesEndpoint($information = null)
	{
		return $this->cache->rememberForever($this->package->travis.'-issues', function() {
			try {
				$opened = $this->client->api('issues')->all($this->package->vendor, $this->package->package, array('state' => 'open'));
				$closed = $this->client->api('issues')->all($this->package->vendor, $this->package->package, array('state' => 'closed'));
				$opened = array_get($opened, '0.number');
				$closed = array_get($closed, '0.number');
			} catch (Exception $e) {
				return array(0, 0);
			}

			return [$opened, $closed];
		});
	}

	/**
	 * Get the core informations
	 *
	 * @return array
	 */
	public function showEndpoint()
	{
		return $this->client->api('repo')->show($this->package->vendor, $this->package->package);
	}
}
