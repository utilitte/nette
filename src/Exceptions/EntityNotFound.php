<?php declare(strict_types = 1);

namespace Utilitte\Nette\Exceptions;

use Nette\Application\BadRequestException;

class EntityNotFound extends BadRequestException
{

	public function __construct(string $class, string $identifier)
	{
		parent::__construct(sprintf('Entity %s(%s) not found', $class, $identifier), 404);
	}

}
