<?php declare(strict_types = 1);

namespace Utilitte\Nette\Strict;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\AccessorDefinition;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ImportedDefinition;
use Nette\DI\Definitions\LocatorDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Utilitte\Php\Strict\Strict;

final class NetteDIStrict
{

	public static function getServiceDefinitionByType(ContainerBuilder $builder, string $type): ServiceDefinition
	{
		return Strict::instanceOf($builder->getDefinitionByType($type), ServiceDefinition::class);
	}
	public static function addServiceDefinition(ContainerBuilder $builder, string $name): ServiceDefinition
	{
		return Strict::instanceOf($builder->addDefinition($name), ServiceDefinition::class);
	}

	public static function getFactoryDefinitionByType(ContainerBuilder $builder, string $type): FactoryDefinition
	{
		return Strict::instanceOf($builder->getDefinitionByType($type), FactoryDefinition::class);
	}

	public static function getAccessorDefinitionByType(ContainerBuilder $builder, string $type): AccessorDefinition
	{
		return Strict::instanceOf($builder->getDefinitionByType($type), AccessorDefinition::class);
	}

	public static function getImportedDefinitionByType(ContainerBuilder $builder, string $type): ImportedDefinition
	{
		return Strict::instanceOf($builder->getDefinitionByType($type), ImportedDefinition::class);
	}

	public static function getLocatorDefinitionByType(ContainerBuilder $builder, string $type): LocatorDefinition
	{
		return Strict::instanceOf($builder->getDefinitionByType($type), LocatorDefinition::class);
	}

}
