<?php

namespace Misantron\QueryBuilder\Exception;

use RuntimeException;

/**
 * Class QueryRuntimeException.
 */
class QueryRuntimeException extends RuntimeException
{
    /**
     * @return QueryRuntimeException
     */
    public static function fetchBeforeExecute(): QueryRuntimeException
    {
        return new static('Query must be executed before data fetching');
    }

    /**
     * @return QueryRuntimeException
     */
    public static function emptySetQueryPart(): QueryRuntimeException
    {
        return new static('Query set must be filled');
    }

    /**
     * @return QueryRuntimeException
     */
    public static function emptyReturningQueryPart(): QueryRuntimeException
    {
        return new static('Returning fields must be set previously');
    }

    /**
     * @return QueryRuntimeException
     */
    public static function havingWithoutGroup(): QueryRuntimeException
    {
        return new static('Using having without group by');
    }
}
