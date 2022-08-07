<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Typertion\Php\Exception\AssertionFailedException;
use Typertion\Php\TypeAssert;
use Utilitte\Nette\Utility\ComponentNameBase64;

final class FlexibleMultiplierByIdentifierFactory
{

	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function createWithCallback(string $entity, callable $factory, callable $callback, iterable $static = []): FlexibleMultiplier
	{
		$multiplier = new FlexibleMultiplier(fn (string $id) => $this->em->getRepository($entity)->find($id), $factory);
		$callback($multiplier);

		if ($static) {
			$this->addComponents($entity, $multiplier, $static);
		}

		return $multiplier;
	}

	public function createBase64WithCallback(string $entity, callable $factory, callable $callback, iterable $static = []): FlexibleMultiplier
	{
		$multiplier = new FlexibleMultiplier(
			fn (string $id) => $this->em->getRepository($entity)->find(ComponentNameBase64::decode($id)), $factory
		);
		$callback($multiplier);

		if ($static) {
			$this->addComponents($entity, $multiplier, $static, true);
		}

		return $multiplier;
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
	private function addComponents(string $entity, FlexibleMultiplier $multiplier, iterable $static, bool $base64 = false): void
	{
		$metadata = $this->em->getClassMetadata($entity);

		foreach ($static as $object) {
			try {
				TypeAssert::instanceOf($object, $entity);
			} catch (AssertionFailedException) {
				throw new InvalidArgumentException(
					sprintf('Entity %s must be instance of %s', get_class($object), $entity)
				);
			}

			$values = $metadata->getIdentifierValues($object);

			if (count($values) !== 1) {
				throw new InvalidArgumentException(sprintf('Entity %s must have exactly 1 identifier', $entity));
			}

			$name = current($values);
			$multiplier->add($object, $base64 ? ComponentNameBase64::encode($name) : $name);
		}
	}

}
