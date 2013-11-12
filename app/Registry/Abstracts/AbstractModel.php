<?php
namespace Registry\Abstracts;

use Arrounded\Traits\JsonAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * An abstract model that consider mutators as set
 */
abstract class AbstractModel extends Model
{
	use JsonAttributes;
}
