<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use JetBrains\PhpStorm\Deprecated;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;

#[Deprecated('Use NetteDIStrict instead of')]
final class CompilerExtensionUtility
{

	public static function getFactoryDefinitionByType(ContainerBuilder $builder, string $type): FactoryDefinition
	{
		return self::assertFactoryDefinition($builder->getDefinitionByType($type));
	}

	public static function assertFactoryDefinition(Definition $definition): FactoryDefinition
	{
		assert($definition instanceof FactoryDefinition);

		return $definition;
	}

	public static function assertServiceDefinition(Definition $definition): ServiceDefinition
	{
		assert($definition instanceof ServiceDefinition);

		return $definition;
	}

}
