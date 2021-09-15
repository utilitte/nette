<?php declare(strict_types = 1);

namespace Utilitte\Nette\Traits\Application;

use Doctrine\ORM\EntityManagerInterface;
use Utilitte\Nette\Exceptions\EntityIsNotValid;
use Utilitte\Nette\Exceptions\EntityNotFound;
use Utilitte\Nette\Exceptions\InvalidArgumentException;

trait LazyEntityGetter
{

	/**
	 * @var mixed[]
	 * @internal
	 */
	private ?array $_entityMeta = null;

	/** @var callable[] */
	private array $_lazyGetterValidators = [];

	/** @internal */
	private EntityManagerInterface $_entityMetaEm;

	final public function injectLazyEntityGetter(EntityManagerInterface $em): void
	{
		$this->_entityMetaEm = $em;
	}

	final protected function addLazyEntityGetterValidator(callable $validator): void
	{
		$this->_lazyGetterValidators[] = $validator;
	}

	/**
	 * @template T
	 * @param class-string<T> $class
	 * @return T|null
	 */
	final protected function getOptionalEntity(string $class, string $parameterName = 'id'): ?object
	{
		if ($this->getParameter($parameterName) === null) {
			return null;
		}

		return $this->getEntity($class, $parameterName);
	}

	/**
	 * @template T
	 * @param class-string<T> $class
	 * @return T
	 */
	final protected function getEntity(string $class, string $parameterName = 'id'): object
	{
		if ($this->_entityMeta === null) {
			$id = $this->getParameter($parameterName);
			$object = $this->_entityMetaEm->find($class, $id);

			if (!$object) {
				throw new EntityNotFound($class, $id);
			}

			foreach ($this->_lazyGetterValidators as $validator) {
				if ($validator($object) === false) {
					throw new EntityIsNotValid($class, $id);
				}
			}

			$this->_entityMeta = [
				'class' => $class,
				'object' => $object,
			];
		} elseif ($this->_entityMeta['class'] !== $class) {
			throw new InvalidArgumentException(
				sprintf('Entity %s already requested, cannot request %s entity', $this->_entityMeta['class'], $class)
			);
		}

		return $this->_entityMeta['object'];
	}

}
