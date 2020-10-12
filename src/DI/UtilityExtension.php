<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Utilitte\Doctrine\DoctrineIdentityExtractor;
use Utilitte\Nette\Latte\Macros;
use Utilitte\Nette\UI\FlexibleMultiplierByIdentifierFactory;

final class UtilityExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'latte' => Expect::structure([
				'enable' => Expect::bool(interface_exists(ILatteFactory::class)),
				'macros' => Expect::structure([
					'confirmMessage' => Expect::string('Do you really want to continue?'),
				]),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('flexibleMultiplier'))
			->setFactory(FlexibleMultiplierByIdentifierFactory::class);

		if (interface_exists(EntityManagerInterface::class) && class_exists(DoctrineIdentityExtractor::class)) {
			$builder->addDefinition($this->prefix('doctrine.identityExtractor'))
				->setType(DoctrineIdentityExtractor::class);
		}
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		if ($config->latte->enable) {
			$service = CompilerExtensionUtility::getFactoryDefinitionByType($builder, ILatteFactory::class);

			$service->getResultDefinition()
				->addSetup('?->onCompile[] = function ($engine) { ?::install($engine->getCompiler(), ?); }', [
					'@self',
					Macros::class,
					(array) $config->latte->macros,
				]);
		}
	}

}
