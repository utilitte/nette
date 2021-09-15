<?php declare(strict_types = 1);

namespace Utilitte\Nette\Interfaces;

use Latte\Compiler;
use Latte\Macros\MacroSet;

interface LatteMacroExtensionInterface
{

	public function install(Compiler $compiler): MacroSet;

}
