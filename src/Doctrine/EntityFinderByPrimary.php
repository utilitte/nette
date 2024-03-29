<?php declare(strict_types = 1);

namespace Utilitte\Nette\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Nette\Application\UI\Component;
use Utilitte\Nette\Exceptions\EntityIsNotValid;
use Utilitte\Nette\Exceptions\EntityNotFound;
use Utilitte\Nette\Exceptions\InvalidArgumentException;

final class EntityFinderByPrimary
{

	/** @var callable[] */
	private array $validators = [];

	/** @var array{parameter:mixed, class:string, object:object}|null */
	private ?array $previous = null;

	private Component $component;

	private string|int|null $parameterValue;

	private bool $parameterSet = false;

	private string $parameterName = 'id';

	public function __construct(
		Component $component,
		private EntityManagerInterface $em,

	)
	{
		$this->component = $component;
	}

	private function getParameterValue(): string|int|null
	{
		if (!$this->parameterSet) {
			$this->parameterValue = $this->component->getParameter($this->parameterName);
		}

		return $this->parameterValue;
	}

	public function setParameterValue(string|int|null $parameterValue): self
	{
		if ($this->parameterSet) {
			throw new LogicException('Cannot set parameter value, parameter value already set.');
		}

		$this->parameterSet = true;
		$this->parameterValue = $parameterValue;

		return $this;
	}

	public function withParameterValue(string|int|null $parameterValue): self
	{
		$self = clone $this;
		$self->parameterSet = true;
		$self->parameterValue = $parameterValue;
		$self->previous = null;

		return $this;
	}

	public function setParameterName(string $parameterName): self
	{
		if ($this->parameterSet) {
			throw new LogicException('Cannot set parameter name, parameter value already set.');
		}

		$this->parameterName = $parameterName;

		return $this;
	}

	public function withParameterName(string $parameterName): self
	{
		$self = clone $this;
		$self->parameterName = $parameterName;
		$self->parameterSet = false;
		$self->previous = null;

		return $self;
	}

	public function addValidator(callable $validator): self
	{
		$this->validators[] = $validator;

		return $this;
	}

	/**
	 * @template T of object
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function getEntityIfParameterPresented(string $class): ?object
	{
		if ($this->getParameterValue() === null) {
			return null;
		}

		return $this->getEntity($class);
	}

	/**
	 * @template T of object
	 * @param class-string<T> $class
	 * @return T|null
	 */
	public function getNullableEntity(string $class): ?object
	{
		$id = $this->getParameterValue();
		if ($this->previous === null) {
			if ($id === null) {
				return null;
			}

			$object = $this->em->find($class, $id);

			if (!$object) {
				return null;
			}

			foreach ($this->validators as $validator) {
				if ($validator($object) === false) {
					throw new EntityIsNotValid($class, $id);
				}
			}

			$this->previous = [
				'parameter' => $id,
				'class' => $class,
				'object' => $object,
			];
		} elseif ($this->previous['class'] !== $class) {
			throw new InvalidArgumentException(
				sprintf('Entity %s already requested, cannot request %s entity.', $this->previous['class'], $class)
			);
		} elseif ($this->previous['parameter'] !== $id) {
			throw new LogicException(
				sprintf(
					'Entity %s with id %s requested, cannot change id to %s.',
					$this->previous['class'],
					(string) $this->previous['parameter'],
					(string) $id,
				)
			);
		}

		/** @var T */
		return $this->previous['object'];
	}

	/**
	 * @template T of object
	 * @param class-string<T> $class
	 * @return T
	 */
	public function getEntity(string $class): object
	{
		$object = $this->getNullableEntity($class);

		if (!$object) {
			throw new EntityNotFound($class, $this->getParameterValue());
		}

		return $object;
	}

}
