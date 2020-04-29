<?php

declare(strict_types = 1);

namespace App\Model\Exception;

use Doctrine\ORM\EntityNotFoundException;

/**
 * Throw this exception when entity is trashed and not retrieved with trashed entity method.
 */
class EntityTrashedException extends EntityNotFoundException
{
}