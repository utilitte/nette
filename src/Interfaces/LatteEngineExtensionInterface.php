<?php declare(strict_types = 1);

namespace Utilitte\Nette\Interfaces;

use Latte\Engine;

interface LatteEngineExtensionInterface
{

	public function extendEngine(Engine $egine): void;

}
