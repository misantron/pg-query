<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use RuntimeException;

/**
 * Class ServerException.
 */
final class ServerException extends RuntimeException
{
    public static function unexpectedConnectionOption(): ServerException
    {
        return new static('Unexpected connection option provided');
    }

    public static function engineFeatureNotAvailable(string $version): ServerException
    {
        return new static("Feature available since {$version} version");
    }
}
