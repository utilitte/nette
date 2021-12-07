<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Presenter;

use Nette\Utils\Strings;

trait PresenterCheckParametersTrait
{

	/**
	 * @param array<string, mixed> $values name => value
	 */
	protected function checkParameters(array $values): void {
		$parameters = $this->getParameters();
		$redirect = false;

		foreach ($values as $name => $value) {
			if (!isset($parameters[$name]) || $parameters[$name] !== Strings::webalize((string) $value)) {
				$parameters[$name] = Strings::webalize((string) $value);
				$redirect = true;
			}
		}

		if ($redirect) {
			$this->redirectPermanent('this', $parameters);
		}
	}


}
