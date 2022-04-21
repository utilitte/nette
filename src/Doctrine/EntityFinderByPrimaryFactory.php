<?php declare(strict_types = 1);

namespace Utilitte\Nette\Doctrine;

use Nette\Application\UI\Component;

interface EntityFinderByPrimaryFactory
{

	public function create(Component $component): EntityFinderByPrimary;

}
