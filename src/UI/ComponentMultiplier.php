<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use ArrayIterator;
use InvalidArgumentException;
use LogicException;
use Nette\Application\UI\Component;
use Nette\ComponentModel\IComponent;

final class ComponentMultiplier extends Component
{

	/** @var callable */
	private $factory;

	/** @var callable */
	private $finder;

	/** @var callable */
	private $decorator;

	/** @var IComponent[] */
	private array $static = [];

	/**
	 * @param IComponent[] $static
	 */
	public function __construct(callable $finder, callable $factory, array $static)
	{
		$this->finder = $finder;
		$this->factory = $factory;
		$this->static = $static;

		foreach ($static as $key => $component) {
			$this->addComponent($component, (string) $key);
		}
 	}

	/**
	 * @inheritDoc
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->static);
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(): array
	{
		return $this->static;
	}

	public function getFirst(): IComponent
	{
		if (!$this->static) {
			throw new LogicException('No component exists in multiplier');
		}

		return $this->static[array_key_first($this->static)];
	}

	protected function createComponent(string $name): ?IComponent
	{
		$object = ($this->finder)($name);

		if ($object === null) {
			return null;
		}

		if ($this->decorator) {
			$object = ($this->decorator)($object);
		}

		return ($this->factory)($object, $this);
	}

}
