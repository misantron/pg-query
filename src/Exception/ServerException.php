<?php

namespace Misantron\QueryBuilder\Exception;

/**
 * Class ServerException.
 */
class ServerException extends \RuntimeException
{
    /**
     * @return ServerException
     */
    public static function unexpectedConnectionOption(): ServerException
    {
        return new static('Unexpected connection option provided');
    }

    /**
     * @param string $version
     *
     * @return ServerException
     */
    public static function engineFeatureNotAvailable(string $version): ServerException
    {
        return new static("Feature available since {$version} version");
    }
}
