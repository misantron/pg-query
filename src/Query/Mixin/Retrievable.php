<?php

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Retrievable.
 */
interface Retrievable
{
    /**
     * @param string $className
     *
     * @return array
     */
    public function fetchAllObject(string $className): array;

    /**
     * @param string $className
     *
     * @return object|null
     */
    public function fetchOneObject(string $className);

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function fetchCallback(callable $callback): array;

    /**
     * @return array
     */
    public function fetchAllAssoc(): array;

    /**
     * @return array|null
     */
    public function fetchOneAssoc();

    /**
     * @return array
     */
    public function fetchKeyValue(): array;

    /**
     * @return array
     */
    public function fetchAllColumn(): array;

    /**
     * @return mixed|null
     */
    public function fetchColumn();
}
