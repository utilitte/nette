<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use ArrayIterator;
use IteratorAggregate;
use Nette\ComponentModel\IComponent;

/**
 * @extends IteratorAggregate<string|int, IComponent>
 */
interface MultiplierInterface extends IteratorAggregate
{

	/**
	 * @return ArrayIterator<string|int, IComponent>
	 */
	public function getIterator(): ArrayIterator;

	/**
	 * @return IComponent[]
	 */
	public function toArray(): array;

}
