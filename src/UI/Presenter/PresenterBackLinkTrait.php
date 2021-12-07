<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI\Presenter;

use Nette\Application\UI\Link;
use Nette\Application\UI\Presenter;
use Nette\Http\Url;

trait PresenterBackLinkTrait
{

	/**
	 * @param mixed[] $params
	 */
	public function linkWithBacklink(Link $link, string $parameterName = 'backlink'): string
	{
		$link->setParameter($parameterName, $link->getComponent()->link('this', [$parameterName => null]));

		return (string) $link;
	}

	/**
	 * @return never
	 */
	public function redirectWithBacklink(Link $link, string $parameterName = 'backlink'): void
	{
		$link->setParameter($parameterName, $link->getComponent()->link('this', [$parameterName => null]));

		$link->getComponent()->redirect($link->getDestination(), $link->getParameters());
	}

	/**
	 * @param mixed[] $params
	 */
	public function tryRedirectBack(string $parameterName = 'backlink'): void
	{
		$backlink = $this->getParameter($parameterName);

		if ($backlink) {
			$this->redirectUrlWithFlashKey($backlink);
		}
	}

	/**
	 * @return never
	 */
	public function redirectUrlWithFlashKey(string $url): void
	{
		$url = new Url($url);

		if (!$url->getQueryParameter(Presenter::FLASH_KEY)) {
			$flashKey = $this->getParameter(Presenter::FLASH_KEY);
			if (is_string($flashKey) && $flashKey !== '') {
				$url->setQueryParameter(Presenter::FLASH_KEY, $flashKey);
			}
		}

		$this->redirectUrl((string) $url);
	}

}
