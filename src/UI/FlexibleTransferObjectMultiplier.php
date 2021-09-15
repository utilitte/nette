<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use Assert\Assertion;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Nette\Utils\Arrays;
use Utilitte\Php\Objects;

final class FlexibleTransferObjectMultiplier
{

	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @template T of object
	 * @phpstan-param class-string<T> $entity
	 * @phpstan-param T[] $static
	 * @param object[] $static
	 */
	public function create(string $entity, callable $factory, callable $transferObjectFactory, iterable $static = []): FlexibleMultiplier
	{
		$multiplier = new FlexibleMultiplier(fn (string $id) => $this->finder($entity, $id, $transferObjectFactory), $factory);

		if ($static) {
			$this->addComponents($entity, $multiplier, $static, $transferObjectFactory);
		}

		return $multiplier;
	}

	private function finder(string $entity, string $id, callable $transferObjectFactory): ?object
	{
		$entity = $this->em->getRepository($entity)->find($id);
		if (!$entity) {
			return null;
		}

		return Arrays::first($transferObjectFactory([$entity]));
	}

	/**
	 * @param object[] $static
	 */
	private function addComponents(string $entity, FlexibleMultiplier $multiplier, iterable $static, callable $transferObjectFactory): void
	{
		$metadata = $this->em->getClassMetadata($entity);

		$objects = $transferObjectFactory($static);

		foreach ($objects as $object) {
			Assertion::isInstanceOf($object, EntityTransferObject::class);

			$values = $metadata->getIdentifierValues($object->getEntity());

			if (count($values) !== 1) {
				throw new InvalidArgumentException(sprintf('Entity %s must have exactly 1 identifier', $entity));
			}

			$multiplier->add($object, current($values));
		}
	}

}
