<?php declare(strict_types = 1);

namespace Utilitte\Nette\Interfaces;

use Nette\Application\UI\Template;

interface LatteTemplateExtensionInterface
{

	public function extendTemplate(Template $template): void;

}
