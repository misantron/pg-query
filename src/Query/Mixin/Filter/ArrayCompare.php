<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin\Filter;

use Misantron\QueryBuilder\Query\Condition\ArrayContainsCondition;
use Misantron\QueryBuilder\Query\Condition\InArrayCondition;
use Misantron\QueryBuilder\Query\Condition\InCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;

/**
 * Trait ArrayCompare.
 *
 * @property FilterGroup $filters
 */
trait ArrayCompare
{
    public function in(string $column, array $values): Filterable
    {
        return $this->andIn($column, $values);
    }

    public function andIn(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'AND')
        );

        return $this;
    }

    public function orIn(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'OR')
        );

        return $this;
    }

    public function notIn(string $column, array $values): Filterable
    {
        return $this->andNotIn($column, $values);
    }

    public function andNotIn(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'AND')
        );

        return $this;
    }

    public function orNotIn(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function inArray(string $column, $value): Filterable
    {
        return $this->andInArray($column, $value);
    }

    /**
     * @param mixed $value
     */
    public function andInArray(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function orInArray(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'OR')
        );

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function notInArray(string $column, $value): Filterable
    {
        return $this->andNotInArray($column, $value);
    }

    /**
     * @param mixed $value
     */
    public function andNotInArray(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'AND')
        );

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function orNotInArray(string $column, $value): Filterable
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'OR')
        );

        return $this;
    }

    public function arrayContains(string $column, array $values): Filterable
    {
        return $this->andArrayContains($column, $values);
    }

    public function andArrayContains(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'AND')
        );

        return $this;
    }

    public function orArrayContains(string $column, array $values): Filterable
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'OR')
        );

        return $this;
    }
}
