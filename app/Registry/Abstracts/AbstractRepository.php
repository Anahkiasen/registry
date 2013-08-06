<?php
namespace Registry\Abstracts;

/**
 * A base repository class
 */
abstract class AbstractRepository
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
		return $this->entries->truncate();
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// MODEL FLOW ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Return an instance of Model
	 *
	 * @param  array  $attributes
	 *
	 * @return AbstractModel
	 */
	public function create(array $attributes)
	{
		return $this->entries->create($attributes);
	}

	/**
	 * Insert multiple entries
	 *
	 * @param  array  $entries
	 *
	 * @return boolean
	 */
	public function insert(array $entries)
	{
		return $this->entries->insert($entries);
	}
}
