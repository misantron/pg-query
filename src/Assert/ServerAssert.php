<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Assert;

use Misantron\QueryBuilder\Exception\ServerException;
use Misantron\QueryBuilder\Server;
use PDO;

/**
 * Class ServerAssert.
 */
final class ServerAssert
{
    private const CONNECTION_OPTIONS = [
        PDO::ATTR_AUTOCOMMIT,
        PDO::ATTR_TIMEOUT,
        PDO::ATTR_ERRMODE,
        PDO::ATTR_CASE,
        PDO::ATTR_CURSOR_NAME,
        PDO::ATTR_CURSOR,
        PDO::ATTR_PERSISTENT,
        PDO::ATTR_STATEMENT_CLASS,
        PDO::ATTR_FETCH_TABLE_NAMES,
        PDO::ATTR_FETCH_CATALOG_NAMES,
        PDO::ATTR_STRINGIFY_FETCHES,
        PDO::ATTR_MAX_COLUMN_LEN,
        PDO::ATTR_EMULATE_PREPARES,
        PDO::ATTR_DEFAULT_FETCH_MODE,
    ];

    /**
     * @param int $option
     *
     * @throws ServerException
     */
    public static function validConnectionOption(int $option): void
    {
        if (!in_array($option, self::CONNECTION_OPTIONS, true)) {
            throw ServerException::unexpectedConnectionOption();
        }
    }

    /**
     * @param Server $server
     * @param string $version
     *
     * @throws ServerException
     */
    public static function engineFeatureAvailable(Server $server, string $version): void
    {
        if ($server->getVersion() && version_compare($server->getVersion(), $version, '<')) {
            throw ServerException::engineFeatureNotAvailable($version);
        }
    }
}
