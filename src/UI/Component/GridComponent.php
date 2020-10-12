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
	public function setColumn(?Html $column): void
	{
		$this->column = $column;
	}

	public function setColumnNumber(int $columnNumber): void
	{
		$this->columnNumber = max(1, $columnNumber);
	}

	public function render(): void
	{
		$component = $this['grid'];
		$template = $this->getTemplate();

		assert($component instanceof Component);
		assert($template instanceof Template);

		$controls = iterator_to_array($component->getComponents(true, IRenderable::class));

		if (!$controls) {
			return;
		}

		$template->render(__DIR__ . '/templates/grid.latte', [
			'controls' => $controls,
			'calculate' => (bool) $this->row,
			'row' => $this->row,
			'column' => $this->column,
			'number' => $this->columnNumber,
		]);
	}

	protected function createComponentGrid(): Component
	{
		return $this->component;
	}

}
