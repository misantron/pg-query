<?php

namespace Misantron\QueryBuilder\Query\Mixin\Filter;

use Misantron\QueryBuilder\Query\Condition\BetweenCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;

/**
 * Trait RangeCompare
 * @package Misantron\QueryBuilder\Query\Mixin\Filter
 *
 * @property FilterGroup $filters
 */
trait RangeCompare
{
    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function between(string $column, array $values)
    {
        return $this->andBetween($column, $values);
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function andBetween(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(BetweenCondition::create($column, $values), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function orBetween(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(BetweenCondition::create($column, $values), 'OR')
        );
        return $this;
    }
}