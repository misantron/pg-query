<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin\Filter;

use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;

/**
 * Trait ValueCompare.
 *
 * @property FilterGroup $filters
 */
trait ValueCompare
{
    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function equals(string $column, $value): Filterable
    {
        return $this->andEquals($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '='), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function notEquals(string $column, $value): Filterable
    {
        return $this->andNotEquals($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andNotEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '!='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orNotEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '!='), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function more(string $column, $value): Filterable
    {
        return $this->andMore($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andMore(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '>'), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orMore(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '>'), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function moreOrEquals(string $column, $value): Filterable
    {
        return $this->andMoreOrEquals($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andMoreOrEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '>='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orMoreOrEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '>='), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function less(string $column, $value): Filterable
    {
        return $this->andLess($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andLess(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '<'), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orLess(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '<'), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function lessOrEquals(string $column, $value): Filterable
    {
        return $this->andLessOrEquals($column, $value);
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andLessOrEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '<='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orLessOrEquals(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(ValueCondition::create($column, $value, '<='), 'OR')
        );

        return $this;
    }
}
