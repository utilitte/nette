<?php declare(strict_types = 1);

namespace Utilitte\Nette\Utility;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\FactoryDefinition;
use Utilitte\Nette\Strict\NetteDIStrict;

final class NetteDIUtility
{

	public static function getLatteFactory(ContainerBuilder $builder): FactoryDefinition
	{
		return NetteDIStrict::getFactoryDefinitionByType($builder, ILatteFactory::class);
	}

	public static function registerMacro(ContainerBuilder $builder, string $class): void
	{
		self::getLatteFactory($builder)
			->getResultDefinition()
				->addSetup('?->onCompile[] = function (Latte\Engine $engine): void { ?::install($engine->getCompiler()); }', [
					'@self',
					$class,
				]);
	}

}
