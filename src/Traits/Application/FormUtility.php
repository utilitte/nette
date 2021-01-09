<?php declare(strict_types = 1);

namespace Utilitte\Nette\Traits\Application;

use Nette\Application\UI\Form;
use Utilitte\Nette\Utility\FormUtility as FormUtil;

trait FormUtility
{

	final private function createFormUtility(Form $form): FormUtil
	{
		return new FormUtil($this, $form);
	}

}
