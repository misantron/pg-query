<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use LogicException;

/**
 * Class QueryLogicException.
 */
final class QueryLogicException extends LogicException
{
    public static function tableAlreadyJoined(): QueryLogicException
    {
        return new static('Table has already joined with same alias');
    }

    public static function aliasAlreadyInUse(): QueryLogicException
    {
        return new static('Table alias is already in use');
    }
}
