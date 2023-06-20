<?php declare(strict_types = 1);

namespace Utilitte\Nette\UI;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Arrays;
use Utilitte\Doctrine\Entity;

final class DoctrineComponentMultiplierFactory
{

	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param class-string $className
	 * @param object[] $entities
	 */
	public function create(string $className, array $entities, callable $factory): ComponentMultiplier
	{
		$static = [];

		foreach ($entities as $entity) {
			if ($entity instanceof Entity) {
				$static[$entity->getUniqueIdentity()->getId()] = $factory($entity);
			} else {
				$static[Arrays::first($this->em->getClassMetadata($className)->getIdentifierValues($entity))] = $factory($entity);
			}
		}

		return new ComponentMultiplier(fn (string $id) => $this->em->find($className, $id), $factory, $static);
	}

}
