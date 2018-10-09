<?php

namespace Misantron\QueryBuilder;

/**
 * Interface Stringable
 * @package Misantron\QueryBuilder
 */
interface Stringable
{
    /**
     * @return string
     */
    public function __toString(): string;
}