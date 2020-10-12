<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\FactoryDefinition;

final class CompilerExtensionUtility
{

	public static function getFactoryDefinitionByType(ContainerBuilder $builder, string $type): FactoryDefinition
	{
		$definition = $builder->getDefinitionByType($type);
		assert($definition instanceof FactoryDefinition);

		return $definition;
	}

}
