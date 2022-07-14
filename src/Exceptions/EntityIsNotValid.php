<?php declare(strict_types = 1);

namespace Utilitte\Nette\Exceptions;

use Nette\Application\BadRequestException;

final class EntityIsNotValid extends BadRequestException
{

	public function __construct(string $class, string|int $identifier)
	{
		parent::__construct(sprintf('Entity %s(%s) did not pass validation', $class, $identifier), 404);
	}

}
