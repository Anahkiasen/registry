<?php
namespace Registry\Abstracts;

use Arrounded\Traits\JsonAttributes;

/**
 * An abstract model that consider mutators as set
 */
abstract class AbstractModel extends \Arrounded\Abstracts\AbstractModel
{
	use JsonAttributes;
}
