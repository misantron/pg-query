<?php

namespace Misantron\QueryBuilder\Assert;

use Misantron\QueryBuilder\Server;

/**
 * Trait Assert.
 *
 *
 * @property Server        $server
 * @property \PDOStatement $statement
 */
trait Assert
{
    /**
     * @param array $items
     *
     * @throws \InvalidArgumentException
     */
    protected function assertValuesNotEmpty($items)
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
    protected function assertColumnsNotEmpty($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }
    }

    /**
     * @throws \RuntimeException
     */
    protected function assertQueryExecuted()
    {
        if (!$this->statement instanceof \PDOStatement) {
            throw new \RuntimeException('Data fetch error: query must be executed before fetch data');
        }
    }

    /**
     * @param string $version
     *
     * @throws \RuntimeException
     */
    protected function assertFeatureAvailable(string $version)
    {
        if ($this->server->getVersion() && version_compare($this->server->getVersion(), $version, '<')) {
            throw new \RuntimeException("Available since {$version} version");
        }
    }
}
