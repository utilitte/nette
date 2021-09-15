<?php declare(strict_types = 1);

namespace Utilitte\Nette\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Utilitte\Nette\Interfaces\LatteMacroExtensionInterface;

final class HrefMacros implements LatteMacroExtensionInterface
{

	public function __construct(
		private ?string $logInLink = null,
	)
	{
	}

	public function install(Compiler $compiler): MacroSet
	{
		$set = new MacroSet($compiler);

		if ($this->logInLink) {
			$set->addMacro('phref-loggedIn', null, null, [$this, 'macroLoggedLink']);
			$set->addMacro('href-loggedIn', null, null, [$this, 'macroLoggedLink']);
		}

		return $set;
	}

	public function macroLoggedLink(MacroNode $node, PhpWriter $writer): string
	{
		$presenterNode = substr($node->name, 0, 1) === 'p';

		$node->modifiers = preg_replace('#\|safeurl\s*(?=\||\z)#i', '', $node->modifiers);
		return ' ?> href="<?php ' .
			$writer->using($node)
				->write(
					'if ($user->isLoggedIn()) { %node.line' . "\n"
					. 'echo %escape(%modify('
					. ($presenterNode ? '$this->global->uiPresenter' : '$this->global->uiControl')
					. '->link(%node.word, %node.array?)));' . "\n"
					. '} else {' . "\n"
					. 'echo %escape(%modify($this->global->uiPresenter->link(%node.var)));' . "\n"
					. '}',
					$this->logInLink
				)
			. ' ?>"<?php ';
	}

}
