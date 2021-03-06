<?php
namespace Registry\Abstracts;

/**
 * A base repository class
 */
abstract class AbstractRepository extends \Arrounded\Abstracts\AbstractRepository
{
	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Flush all entries
	 *
	 * @return boolean
	 */
	public function flush()
	{
		return $this->items->truncate();
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// MODEL FLOW ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Insert multiple entries
	 *
	 * @param  array  $entries
	 *
	 * @return boolean
	 */
	public function insert(array $entries)
	{
		return $this->items->insert($entries);
	}
}
