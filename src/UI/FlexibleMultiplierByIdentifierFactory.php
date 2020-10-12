<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Utilitte\Php\Objects;

final class FlexibleMultiplierByIdentifierFactory
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
	public function create(string $entity, callable $factory, iterable $static = []): FlexibleMultiplier
	{
		$multiplier = new FlexibleMultiplier(fn (string $id) => $this->em->getRepository($entity)->find($id), $factory);

		if ($static) {
			$this->addComponents($entity, $multiplier, $static);
		}

		return $multiplier;
	}

	/**
	 * @param object[] $static
	 */
	private function addComponents(string $entity, FlexibleMultiplier $multiplier, iterable $static): void
	{
		$metadata = $this->em->getClassMetadata($entity);

		foreach ($static as $object) {
			if (!Objects::instanceOf($object, $entity)) {
				throw new InvalidArgumentException(
					sprintf('Entity %s must be instance of %s', get_class($object), $entity)
				);
			}

			$values = $metadata->getIdentifierValues($object);

			if (count($values) !== 1) {
				throw new InvalidArgumentException(sprintf('Entity %s must have exactly 1 identifier', $entity));
			}

			$multiplier->add($object, current($values));
		}
	}

}
