<?php declare(strict_types = 1);

namespace Utilitte\Nette\Traits\Application;

use Utilitte\Nette\Doctrine\EntityFinderByPrimary;
use Utilitte\Nette\Doctrine\EntityFinderByPrimaryFactory;

trait EntityFinderByPrimaryTrait
{

	private EntityFinderByPrimary $entityFinderByPrimary;
	
	final public function injectEntityFinderByPrimary(EntityFinderByPrimaryFactory $factory): void
	{
		$this->entityFinderByPrimary = $factory->create($this);
	}

}
