<?php

namespace Misantron\QueryBuilder\Exception;

use LogicException;

/**
 * Class QueryLogicException.
 */
class QueryLogicException extends LogicException
{
    /**
     * @return QueryLogicException
     */
    public static function tableAlreadyJoined(): QueryLogicException
    {
        return new static('Table has already joined with same alias');
    }

    /**
     * @return QueryLogicException
     */
    public static function aliasAlreadyInUse(): QueryLogicException
    {
        return new static('Table alias is already in use');
    }
}
