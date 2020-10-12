<?php declare(strict_types = 1);

namespace Utilitte\Nette\Traits\Application;

use Doctrine\ORM\EntityManagerInterface;
use Utilitte\Nette\Exceptions\EntityNotFound;
use Utilitte\Nette\Exceptions\InvalidArgumentException;

trait LazyEntityGetter
{

	/**
	 * @var mixed[]
	 * @internal
	 */
	private ?array $_entityMeta = null;

	/** @internal */
	private EntityManagerInterface $_entityMetaEm;

	final public function injectLazyEntityGetter(EntityManagerInterface $em): void
	{
		$this->_entityMetaEm = $em;
	}

	final protected function getEntity(string $class, string $parameterName = 'id'): object
	{
		if ($this->_entityMeta === null) {
			$id = $this->getParameter($parameterName);
			$object = $this->_entityMetaEm->find($class, $id);

			if (!$object) {
				throw new EntityNotFound($class, $id);
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
