<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Component;

use InvalidArgumentException;
use Nette\Application\UI\Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\IRenderable;
use Nette\Application\UI\Multiplier;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Html;
use function assert;

final class GridComponent extends Control
{

	private Component $component;

	/**
	 * @phpstan-var Html<int, Html|string>|null
	 */
	private ?Html $row = null;

	/**
	 * @phpstan-var Html<int, Html|string>|null
	 */
	private ?Html $column = null;

	/**
	 * @phpstan-var Html<int, Html|string>|null
	 */
	private ?Html $container = null;

	private ?string $incrementSequencePrefix = null;

	private int $columnNumber = 1;

	/** @var mixed[] */
	private array $prepends = [];

	/** @var Component[] */
	private array $prependsById = [];

	public function __construct(Component $component)
	{
		$this->component = $component;
	}

	public static function create(Component $component): self
	{
		return new static($component);
	}

	/**
	 * @phpstan-param Html<int, Html|string>|null $row
	 */
	public function setRow(?Html $row): self
	{
		$this->row = $row;

		return $this;
	}

	/**
	 * @phpstan-param Html<int, Html|string>|null $row
	 */
	public function setContainer(?Html $container): self
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * @phpstan-param Html<int, Html|string>|null $column
	 */
	public function setColumn(?Html $column): self
	{
		$this->column = $column;

		return $this;
	}

	public function setColumnNumber(int $columnNumber): self
	{
		$this->columnNumber = max(1, $columnNumber);

		return $this;
	}

	public function setIncrementSequence(string $prefix): self
	{
		$this->incrementSequencePrefix = $prefix;

		return $this;
	}

	public function addComponentAtPosition(string $id, int $position, Component $control): self
	{
		$this->prependsById[$id] = $control;
		$this->prepends[$position][] = $id;

		return $this;
	}

	public function render(): void
	{
		$component = $this['grid'];
		$template = $this->getTemplate();

		assert($component instanceof Component);
		assert($template instanceof Template);

		$controls = iterator_to_array($component->getComponents(false, IRenderable::class));

		if (!$controls) {
			return;
		}

		// column functions
		$template->getLatte()->addFunction('startColumn', function (int $counter): ?string {
			if (!$this->column) {
				return null;
			}

			$col = $this->column;

			if ($this->incrementSequencePrefix) {
				$col = clone $this->column;
				$col->appendAttribute('class', $this->incrementSequencePrefix . $counter);
			}

			return $col->startTag();
		});
		$template->getLatte()->addFunction('endColumn', function (): ?string {
			if (!$this->column) {
				return null;
			}

			return $this->column->endTag();
		});

		// row functions
		$template->getLatte()->addFunction('startRow', function (int $counter): ?string {
			if (!$this->row) {
				return null;
			}

			return $this->row->startTag();
		});
		$template->getLatte()->addFunction('endRow', function (): ?string {
			if (!$this->row) {
				return null;
			}

			return $this->row->endTag();
		});

		$controls = $this->mergePrepends($controls);

		$template->render(__DIR__ . '/templates/grid.latte', [
			'controls' => $controls,
			'calculate' => (bool) $this->row,
			'number' => $this->columnNumber,
			'containerStart' => $this->container?->startTag(),
			'containerEnd' => $this->container?->endTag(),
		]);
	}

	protected function createComponentGrid(): Component
	{
		return $this->component;
	}

	protected function createComponentPrepend(): Component
	{
		return new Multiplier(function (string $index) {
			if (!isset($this->prependsById[$index])) {
				throw new InvalidArgumentException(sprintf('Prepended component %d not exists', $index));
			}

			return $this->prependsById[$index];
		});
	}

	/**
	 * @param Component[] $controls
	 * @return Component[]
	 */
	private function mergePrepends(array $controls): array
	{
		if (!$this->prepends) {
			return $controls;
		}

		$return = [];

		$position = 1;

		foreach ($controls as $control) {
			if (isset($this->prepends[$position])) {
				foreach ($this->prepends[$position] as $id) {
					$return[] = $this->getComponent(sprintf('prepend-%s', $id));
				}
			}

			$return[] = $control;

			$position++;
		}

		return $return;
	}

}
