<?php declare(strict_types = 1);

namespace Utilitte\Nette\Traits\Application;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Nette\Utils\Strings;
use Utilitte\Nette\Exceptions\EntityIsNotValid;
use Utilitte\Nette\Exceptions\EntityNotFound;
use Utilitte\Nette\Exceptions\InvalidArgumentException;

trait LazyEntityGetter
{

	/** @var mixed[] */
	private ?array $_entityMeta = null;

	/** @var callable[] */
	private array $_lazyGetterValidators = [];

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
	final protected function getEntityIfParameterPresented(string $class, string $parameterName = 'id', array $checkParameters = []): ?object
	{
		if ($this->getParameter($parameterName) === null) {
			return null;
		}

		return $this->getEntity($class, $parameterName, $checkParameters);
	}

	final protected function getNullableEntity(string $class, string $parameterName = 'id', array $checkParameters = []): ?object
	{
		if ($this->_entityMeta === null) {
			$object = $this->getOptionalEntity($class, $parameterName, $checkParameters);
			$id = $this->getParameter($parameterName);

			if ($id === null) {
				return null;
			}

			$object = $this->_entityMetaEm->find($class, $id);

			if (!$object) {
				return null;
			}

			// checks parameters if equals or fix
			$redirect = false;
			$parameters = $this->getParameters();
			foreach ($checkParameters as $name => $value) {
				$parameter = $parameters[$name] ?? null;
				if ($parameter === null && $value === null) {
					continue;
				}
				if ($parameter !== ($webalize = Strings::webalize((string) $value))) {
					$parameters[$name] = $webalize;
					$redirect = true;
				}
			}

			if ($redirect) {
				$this->redirectPermanent('this', $parameters);
			}

			foreach ($this->_lazyGetterValidators as $validator) {
				if ($validator($object) === false) {
					throw new EntityIsNotValid($class, $id);
				}
			}

			$this->_entityMeta = [
				'parameter' => $id,
				'class' => $class,
				'object' => $object,
			];
		} elseif ($this->_entityMeta['class'] !== $class) {
			throw new InvalidArgumentException(
				sprintf('Entity %s already requested, cannot request %s entity.', $this->_entityMeta['class'], $class)
			);
		} elseif ($this->_entityMeta['parameter'] !== $this->getParameter($parameterName)) {
			throw new LogicException(
				sprintf(
					'Entity %s with id %s requested, cannot change id to %s.',
					$this->_entityMeta['class'],
					(string) $this->_entityMeta['parameter'],
					(string) $this->getParameter($parameterName)
				)
			);
		}

		return $this->_entityMeta['object'];
	}

	/**
	 * @template T
	 * @param class-string<T> $class
	 * @return T
	 */
	final protected function getEntity(string $class, string $parameterName = 'id', array $checkParameters = []): object
	{
		$object = $this->getNullableEntity($class, $parameterName, $checkParameters);

		if (!$object) {
			throw new EntityNotFound($class, $id);
		}

		return $object;
	}

}
