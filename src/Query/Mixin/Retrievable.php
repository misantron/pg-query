<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Retrievable.
 */
interface Retrievable
{
    public function fetchAllObject(string $className): array;

    /**
     * @return object|null
     */
    public function fetchOneObject(string $className);

    public function fetchCallback(callable $callback): array;

    public function fetchAllAssoc(): array;

    public function fetchOneAssoc(): ?array;

    public function fetchKeyValue(): array;

    public function fetchAllColumn(): array;

    /**
     * @return mixed|null
     */
    public function fetchColumn();
}
