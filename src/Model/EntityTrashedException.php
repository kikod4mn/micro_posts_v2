<?php

namespace App\Model;

use Doctrine\ORM\EntityNotFoundException;

/**
 * Throw this exception when entity is trashed and not retrieved with trashed entity method.
 */
class EntityTrashedException extends EntityNotFoundException
{
}