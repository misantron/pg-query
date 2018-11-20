<?php

namespace Misantron\QueryBuilder\Assert;

/**
 * Trait Assert.
 */
trait Assert
{
    /**
     * @param array $data
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
}
