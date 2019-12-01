<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder;

/**
 * Interface Compilable.
 */
interface Compilable
{
    public function compile(): string;
}
