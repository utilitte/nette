<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Component;

use Nette\Application\UI\Component;

final class ComponentCollection extends Component
{

	/**
	 * @param array<string|int, Component> $components
	 */
	public function __construct(array $components)
	{
		foreach ($components as $name => $component) {
			$this->addComponent($component, (string) $name);
		}
	}

}
