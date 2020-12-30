<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Component;

use LogicException;
use Nette\Application\UI\Control;
use Nette\Utils\Html;

final class WrapComponent extends Control
{

	private Control $control;

	/** @var Html|string */
	private $wrapper;

	/**
	 * @param Html|string $wrapper
	 */
	public function __construct(Control $control, $wrapper)
	{
		$this->control = $control;
		$this->wrapper = $wrapper;
	}

	public function render(): void
	{
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . '/templates/wrap.latte');

		$template->ctrl = $this['control'];
		[$template->startTag, $template->endTag] = $this->getTags();
		$template->wrapper = $this->wrapper;

		$template->render();
	}

	protected function createComponentControl(): Control
	{
		return $this->control;
	}

	/**
	 * @return string[]
	 */
	private function getTags(): array
	{
		if ($this->wrapper instanceof Html) {
			return [$this->wrapper->startTag(), $this->wrapper->endTag()];
		} elseif (is_string($this->wrapper)) {
			return explode('$$', $this->wrapper);
		} else {
			throw new LogicException(sprintf('Wrapper must be instance of %s or a string', Html::class));
		}
	}

}
