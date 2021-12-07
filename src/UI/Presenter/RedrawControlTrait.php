<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Presenter;

trait RedrawControlTrait
{

	/**
	 * @param string|string[] $snippets
	 * @param mixed[] $args
	 */
	public function redraw($snippets = null, string $link = 'this', array $args = []): void
	{
		if ($this->isAjax()) {
			foreach ((array) $snippets as $snippet) {
				$this->redrawControl($snippet);
			}
		} else {
			$this->redirect($link, $args);
		}
	}

}
