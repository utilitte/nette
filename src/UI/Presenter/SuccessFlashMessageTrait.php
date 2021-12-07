<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Presenter;

use Nette\HtmlStringable;
use stdClass;

trait SuccessFlashMessageTrait
{

	/**
	 * @param string|stdClass|HtmlStringable $message
	 */
	public function flashMessage($message, string $type = 'success'): stdClass
	{
		return parent::flashMessage($message, $type);
	}

}
