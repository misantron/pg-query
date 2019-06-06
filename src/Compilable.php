<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder;

/**
 * Interface Compilable.
 */
interface Compilable
{
    /**
     * @return string
     */
    public function compile(): string;
}
