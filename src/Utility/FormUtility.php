<?php declare(strict_types = 1);

namespace Utilitte\Nette\Utility;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\UI\Link;
use Nette\Forms\Controls\BaseControl;

final class FormUtility
{

	private Control $control;

	private Form $form;

	/** @var callable[] */
	private array $onSuccess = [];

	/** @var callable[] */
	private array $onError = [];

	/** @var bool[] */
	private array $useChecker = [];

	public function __construct(Control $control, Form $form)
	{
		$this->control = $control;
		$this->form = $form;

		$form->onAnchor[] = function () {
			$this->form->onSuccess[] = fn () => $this->onSuccess();
			$this->form->onError[] = fn () => $this->onError();
		};
	}

	public function successFlashMessage(string $message): self
	{
		$this->onSuccess[] = fn () => $this->control->flashMessage($message);

		return $this;
	}

	public function errorsToFlashMessages(): self
	{
		$this->checkUse(__METHOD__);

		$this->onError[] = function (): void {
			foreach ($this->form->getOwnErrors() as $error) {
				$this->control->flashMessage($error, 'error');
			}

			/** @var BaseControl $control */
			foreach ($this->form->getControls() as $control) {
				foreach ($control->getErrors() as $error) {
					$this->control->flashMessage($control->caption . ': ' . $error, 'error');
				}
			}
		};

		return $this;
	}

	public function redirectWithBacklink(string $parameterName = 'backlink'): self
	{
		$this->checkUse('redirect');

		$backlink = $this->control->getParameter($parameterName);

		if (!$backlink) {
			return $this;
		}

		$this->form->onAnchor[] = function () use ($backlink): void {
			$link = $this->form->getAction();

			if ($link instanceof Link) {
				$link->setParameter('backlink', $backlink);
			}
		};

		$this->onSuccess[] = fn () => $this->control->getPresenter()->redirectUrl($backlink);

		return $this;
	}

	public function redirectOnSuccess(string $destination, array $args = [], ?Control $control = null) {
		$this->checkUse('redirect');

		$this->onSuccess[] = fn () => ($control ?? $this->control)->redirect($destination, $args);

		return $this;
	}

	/**
	 * @param string[] $snippets
	 */
	public function refreshOnSuccess(array $snippets = []): self
	{
		$this->checkUse('redirect');

		$presenter = $this->control->getPresenterIfExists();
		if ($presenter && $presenter->isAjax()) {
			$this->onSuccess[] = function () use ($snippets): void {
				foreach ($snippets as $snippet) {
					$this->control->redrawControl($snippet);
				}
			};
		} else {
			$this->onSuccess[] = fn () => $this->control->redirect('this');
		}

		return $this;
	}

	private function checkUse(string $name): void {
		if (isset($this->useChecker[$name])) {
			throw new \LogicException('Cannot call ' . $name . ' twice.');
		}

		$this->useChecker[$name] = true;
	}

	private function onSuccess(): void
	{
		foreach ($this->onSuccess as $success) {
			$success();
		}
	}

	private function onError(): void
	{
		foreach ($this->onError as $onError) {
			$onError();
		}
	}

}
