<?php declare(strict_types = 1);

namespace Utilitte\Nette\DI;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Utilitte\Doctrine\DoctrineIdentityExtractor;
use Utilitte\Doctrine\FetchByIdentifiers;
use Utilitte\Doctrine\Query\RawQueryFactory;
use Utilitte\Doctrine\QueryMetadataExtractor;
use Utilitte\Nette\Http\MaxUploadSize;
use Utilitte\Nette\Latte\Macros;
use Utilitte\Nette\UI\FlexibleMultiplierByIdentifierFactory;
use Utilitte\Nette\UI\FlexibleTransferObjectMultiplier;

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

		$builder->addDefinition($this->prefix('image'))
			->setFactory(MaxUploadSize::class);

		$builder->addDefinition($this->prefix('flexibleMultiplier'))
			->setFactory(FlexibleMultiplierByIdentifierFactory::class);

		$builder->addDefinition($this->prefix('flexibleTransferObjectMultiplier'))
			->setFactory(FlexibleTransferObjectMultiplier::class);

		if (interface_exists(EntityManagerInterface::class) && class_exists(DoctrineIdentityExtractor::class)) {
			$builder->addDefinition($this->prefix('doctrine.identityExtractor'))
				->setType(DoctrineIdentityExtractor::class);

			$builder->addDefinition($this->prefix('doctrine.queryMetadataExtractor'))
				->setType(QueryMetadataExtractor::class);

			$builder->addDefinition($this->prefix('doctrine.rawQueryFactory'))
				->setType(RawQueryFactory::class);

			$builder->addDefinition($this->prefix('doctrine.fetchByIdentifiers'))
				->setType(FetchByIdentifiers::class);
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
