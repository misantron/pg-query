<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

use Misantron\QueryBuilder\Assert\QueryAssert;
use PDO;
use PDOStatement;

/**
 * Trait DataFetch.
 *
 * @property PDOStatement $statement
 */
trait DataFetching
{
    public function fetchAllObject(string $className): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_CLASS, $className);
    }

    /**
     * @return object|null
     */
    public function fetchOneObject(string $className)
    {
        QueryAssert::queryExecuted($this->statement);

        $response = $this->statement->fetchObject($className);

        return $response instanceof $className ? $response : null;
    }

    public function fetchCallback(callable $callback): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_FUNC, $callback);
    }

    public function fetchAllAssoc(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOneAssoc(): ?array
    {
        QueryAssert::queryExecuted($this->statement);

        $response = $this->statement->fetch(PDO::FETCH_ASSOC);

        return is_array($response) ? $response : null;
    }

    public function fetchKeyValue(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function fetchAllColumn(): array
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_COLUMN);
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
