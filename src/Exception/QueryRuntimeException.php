<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use RuntimeException;

/**
 * Class QueryRuntimeException.
 */
final class QueryRuntimeException extends RuntimeException
{
    public static function fetchBeforeExecute(): QueryRuntimeException
    {
        return new static('Query must be executed before data fetching');
    }

    public static function emptySetQueryPart(): QueryRuntimeException
    {
        return new static('Query set must be filled');
    }

    public static function emptyReturningQueryPart(): QueryRuntimeException
    {
        return new static('Returning fields must be set previously');
    }

    public static function havingWithoutGroup(): QueryRuntimeException
    {
        return new static('Using having without group by');
    }
}
