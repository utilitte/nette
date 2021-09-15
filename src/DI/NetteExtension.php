<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Utilitte\Nette\Interfaces\LatteEngineExtensionInterface;
use Utilitte\Nette\Interfaces\LatteFilterLoaderExtensionInterface;
use Utilitte\Nette\Interfaces\LatteMacroExtensionInterface;
use Utilitte\Nette\Interfaces\LatteTemplateExtensionInterface;

final class NetteExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$factory = CompilerExtensionUtility::assertFactoryDefinition($builder->getDefinitionByType(LatteFactory::class));
		$result = $factory->getResultDefinition();

		foreach ($builder->findByType(LatteEngineExtensionInterface::class) as $definition) {
			$result->addSetup('?->extendEngine(?);', [$definition, '@self']);
		}

		foreach ($builder->findByType(LatteFilterLoaderExtensionInterface::class) as $definition) {
			$result->addSetup('?->addFilterLoader([?, "_invoke"]);', ['@self', $definition]);
		}

		foreach ($builder->findByType(LatteMacroExtensionInterface::class) as $definition) {
			$result->addSetup('?->onCompile[] = fn ($engine) => ?->install($engine->getCompiler());', ['@self', $definition]);
		}

		$factory = CompilerExtensionUtility::assertServiceDefinition($builder->getDefinitionByType(TemplateFactory::class));

		foreach ($builder->findByType(LatteTemplateExtensionInterface::class) as $definition) {
			$factory->addSetup('?->onCreate[] = fn ($template) => ?->extendTemplate($template);', ['@self', $definition]);
		}
	}

}
