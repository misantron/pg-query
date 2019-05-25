<?php

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
