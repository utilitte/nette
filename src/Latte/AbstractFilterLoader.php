<?php declare(strict_types = 1);

namespace Utilitte\Nette\Latte;

use Utilitte\Nette\Interfaces\LatteFilterLoaderExtensionInterface;

abstract class AbstractFilterLoader implements LatteFilterLoaderExtensionInterface
{

	final public function _invoke(string $name): ?callable
	{
		if ($name === __METHOD__) {
			return null;
		}

		return method_exists($this, $name) ? [$this, $name] : null;
	}

}
