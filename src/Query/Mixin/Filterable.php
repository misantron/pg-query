<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Filterable.
 */
interface Filterable
{
    /**
     * @return Filterable
     */
    public function beginGroup(): Filterable;

    /**
     * @return Filterable
     */
    public function andGroup(): Filterable;

    /**
     * @return Filterable
     */
    public function orGroup(): Filterable;

    /**
     * @return Filterable
     */
    public function endGroup(): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function equals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function notEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andNotEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orNotEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function more(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andMore(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orMore(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function moreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andMoreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orMoreOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function less(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andLess(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orLess(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function lessOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andLessOrEquals(string $column, $value): Filterable;

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orLessOrEquals(string $column, $value): Filterable;
}
