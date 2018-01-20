<?php

namespace MediaTech\Query\Query\Mixin;


/**
 * Trait DataFetch
 * @package MediaTech\Query\Query\Mixin
 *
 * @property \PDOStatement $statement
 */
trait DataFetching
{
    /**
     * @param string $className
     * @return array
     */
    public function fetchAllObject(string $className): array
    {
        $this->validateFetch();

        return $this->statement->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    /**
     * @param string $className
     * @return object|null
     */
    public function fetchOneObject(string $className)
    {
        $this->validateFetch();

        $response = $this->statement->fetchObject($className);

        return $response instanceof $className ? $response : null;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function fetchCallback(callable $callback): array
    {
        $this->validateFetch();

        return $this->statement->fetchAll(\PDO::FETCH_FUNC, $callback);
    }

    /**
     * @return array
     */
    public function fetchAllAssoc(): array
    {
        $this->validateFetch();

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array|null
     */
    public function fetchOneAssoc()
    {
        $this->validateFetch();

        $response = $this->statement->fetch(\PDO::FETCH_ASSOC);

        return is_array($response) ? $response : null;
    }

    /**
     * @return array
     */
    public function fetchKeyValue(): array
    {
        $this->validateFetch();

        return $this->statement->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * @return array
     */
    public function fetchAllColumn(): array
    {
        $this->validateFetch();

        return $this->statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @return mixed|null
     */
    public function fetchColumn()
    {
        $this->validateFetch();

        $response = $this->statement->fetchColumn();

        return $response === false ? null : $response;
    }

    /**
     * @throws \RuntimeException
     */
    private function validateFetch()
    {
        if (!$this->statement instanceof \PDOStatement) {
            throw new \RuntimeException('Data fetch error: query must be executed before fetch data');
        }
    }
}