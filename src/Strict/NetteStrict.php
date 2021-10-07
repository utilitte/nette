<?php declare(strict_types = 1);

namespace Utilitte\Nette\Strict;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Forms\Controls\BaseControl;
use stdClass;

final class NetteStrict
{

	public static function template(object $template): Template
	{
		assert($template instanceof Template);

		return $template;
	}

	public static function stdClass(object $stdClass): stdClass
	{
		assert($stdClass instanceof stdClass);

		return $stdClass;
	}

	public static function formControl(object $control): BaseControl
	{
		assert($control instanceof BaseControl);

		return $control;
	}

}
