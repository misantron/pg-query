<?php

namespace Misantron\QueryBuilder\Query\Mixin;

use Misantron\QueryBuilder\Assert\QueryAssert;

/**
 * Trait DataFetch.
 *
 *
 * @property \PDOStatement $statement
 */
trait DataFetching
{
    /**
     * @param string $className
     *
     * @return array
     */
    public function fetchAllObject(string $className): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    /**
     * @param string $className
     *
     * @return object|null
     */
    public function fetchOneObject(string $className)
    {
        QueryAssert::queryExecuted($this->statement);

        $response = $this->statement->fetchObject($className);

        return $response instanceof $className ? $response : null;
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function fetchCallback(callable $callback): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(\PDO::FETCH_FUNC, $callback);
    }

    /**
     * @return array
     */
    public function fetchAllAssoc(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array|null
     */
    public function fetchOneAssoc(): ?array
    {
        QueryAssert::queryExecuted($this->statement);

        $response = $this->statement->fetch(\PDO::FETCH_ASSOC);

        return is_array($response) ? $response : null;
    }

    /**
     * @return array
     */
    public function fetchKeyValue(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * @return array
     */
    public function fetchAllColumn(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @return mixed|null
     */
    public function fetchColumn()
    {
        QueryAssert::queryExecuted($this->statement);

        $response = $this->statement->fetchColumn();

        return $response === false ? null : $response;
    }
}
