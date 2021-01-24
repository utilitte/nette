<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use ArrayIterator;
use InvalidArgumentException;
use LogicException;
use Nette\Application\UI\Component;
use Nette\ComponentModel\IComponent;

class FlexibleMultiplier extends Component implements MultiplierInterface
{

	/** @var callable */
	private $factory;

	/** @var callable */
	private $getter;

	/** @var IComponent[] */
	private array $static = [];

	public function __construct(callable $getter, callable $factory)
	{
		$this->getter = $getter;
		$this->factory = $factory;
	}

	/**
	 * @param object $entityOrComponent
	 * @param string|int $name
	 */
	public function add($entityOrComponent, $name): IComponent
	{
		if (empty($name) && $name !== 0) {
			throw new InvalidArgumentException('Argument $name must not be an empty');
		}

		$component = $entityOrComponent;
		if (!$component instanceof Component) {
			$component = ($this->factory)($entityOrComponent);
		}

		$this->addComponent($component, (string) $name);

		return $this->static[] = $component;
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
		$entity = ($this->getter)($name);

		if ($entity === null) {
			return null;
		}

		return ($this->factory)($entity, $this);
	}

}
