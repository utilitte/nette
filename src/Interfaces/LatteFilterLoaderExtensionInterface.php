<?php declare(strict_types = 1);

namespace Utilitte\Nette\Interfaces;

interface LatteFilterLoaderExtensionInterface
{

	public function _invoke(string $name): ?callable;

}
