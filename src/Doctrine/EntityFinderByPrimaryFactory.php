<?php declare(strict_types = 1);

namespace Utilitte\Nette\Doctrine;

use Nette\Application\IPresenter;

interface EntityFinderByPrimaryFactory
{

	public function create(IPresenter $presenter): EntityFinderByPrimary;

}
