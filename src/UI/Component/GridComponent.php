<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Component;

use Nette\Application\UI\Component;
use Nette\Application\UI\Control;
use Nette\Application\UI\IRenderable;
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

	private ?string $incrementSequencePrefix = null;

	private int $columnNumber = 1;

	public function __construct(Component $component)
	{
		$this->component = $component;
	}

	/**
	 * @phpstan-param Html<int, Html|string>|null $row
	 */
	public function setRow(?Html $row): void
	{
		$this->row = $row;
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


		$template->render(__DIR__ . '/templates/grid.latte', [
			'controls' => $controls,
			'calculate' => (bool) $this->row,
			'number' => $this->columnNumber,
		]);
	}

	protected function createComponentGrid(): Component
	{
		return $this->component;
	}

}
