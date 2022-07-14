<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Utilitte\Nette\Doctrine\EntityFinderByPrimaryFactory;
use Utilitte\Nette\Interfaces\LatteEngineExtensionInterface;
use Utilitte\Nette\Interfaces\LatteFilterLoaderExtensionInterface;
use Utilitte\Nette\Interfaces\LatteMacroExtensionInterface;
use Utilitte\Nette\Interfaces\LatteTemplateExtensionInterface;
use Utilitte\Nette\UI\FlexibleMultiplierByIdentifierFactory;
use Utilitte\Nette\UI\FlexibleTransferObjectMultiplier;

final class NetteExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$factory = $builder->getDefinitionByType(LatteFactory::class);
		assert($factory instanceof FactoryDefinition);

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

		$factory = $builder->getDefinitionByType(TemplateFactory::class);
		assert($factory instanceof ServiceDefinition);

		foreach ($builder->findByType(LatteTemplateExtensionInterface::class) as $definition) {
			$factory->addSetup('?->onCreate[] = fn ($template) => ?->extendTemplate($template);', ['@self', $definition]);
		}

		$builder->addDefinition($this->prefix('flexibleMultiplier'))
			->setFactory(FlexibleMultiplierByIdentifierFactory::class);

		$builder->addDefinition($this->prefix('flexibleTransferObjectMultiplier'))
			->setFactory(FlexibleTransferObjectMultiplier::class);

		$builder->addFactoryDefinition($this->prefix('entityPresenterFinder'))
			->setImplement(EntityFinderByPrimaryFactory::class);
	}

}
