<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Filterable.
 */
interface Filterable
{
    public function beginGroup(): Filterable;

    public function andGroup(): Filterable;

    public function orGroup(): Filterable;

    public function endGroup(): Filterable;

    /**
     * @param mixed $value
     */
    public function equals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function notEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andNotEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orNotEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function more(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andMore(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orMore(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function moreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andMoreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orMoreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function less(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andLess(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orLess(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function lessOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function andLessOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     */
    public function orLessOrEquals(string $column, $value): Filterable;
}
