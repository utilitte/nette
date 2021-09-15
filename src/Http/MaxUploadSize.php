<?php declare(strict_types = 1);

namespace Utilitte\Nette\Http;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Utilitte\Php\Numbers;

final class MaxUploadSize
{

	private int $maxBytes;

	public function __construct(
		?int $maxBytes = null,
	)
	{
		$this->maxBytes = $maxBytes ?? Helpers::iniGetSize('upload_max_filesize');
	}

	public function get(): int
	{
		return $this->maxBytes;
	}

	public function toReadableString(): string
	{
		return Numbers::bytes($this->maxBytes);
	}

	public function applyFormControlRule(BaseControl $control, string $message = 'The size of the uploaded file can be up to %s.'): void
	{
		$rules = $control->getRules();
		$rules->removeRule(Form::MAX_FILE_SIZE);
		$rules->addRule(Form::MAX_FILE_SIZE, sprintf($message, $this->toReadableString()), $this->maxBytes);
	}

}
