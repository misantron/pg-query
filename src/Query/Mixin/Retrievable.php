<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Retrievable.
 */
interface Retrievable
{
    /**
     * @return array
     */
    public function fetchAllObject(string $className): array;

    /**
     * @return object|null
     */
    public function fetchOneObject(string $className);

    /**
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
    public function fetchOneAssoc(): ?array;

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
