<?php

namespace Misantron\QueryBuilder\Assert;

use Misantron\QueryBuilder\Server;

/**
 * Class Assert.
 *
 *
 * @property Server        $server
 * @property \PDOStatement $statement
 */
class Assert
{
    /**
     * @param array $items
     *
     * @throws \InvalidArgumentException
     */
    public static function valuesNotEmpty($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Value list is empty');
        }
    }

    /**
     * @param array|string $items
     *
     * @throws \InvalidArgumentException
     */
    public static function columnsNotEmpty($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }
    }

    /**
     * @param \PDOStatement $statement
     *
     * @throws \RuntimeException
     */
    public static function queryExecuted($statement)
    {
        if (!$statement instanceof \PDOStatement) {
            throw new \RuntimeException('Data fetch error: query must be executed before fetch data');
        }
    }

    /**
     * @param array $returning
     */
    public static function returningConditionSet(array $returning)
    {
        if (empty($returning)) {
            throw new \RuntimeException('Data fetch error: returning fields must be set');
        }
    }

    /**
     * @param Server $server
     * @param string $version
     *
     * @throws \RuntimeException
     */
    public static function featureAvailable(Server $server, string $version)
    {
        if ($server->getVersion() && version_compare($server->getVersion(), $version, '<')) {
            throw new \RuntimeException("Available since {$version} version");
        }
    }
}
