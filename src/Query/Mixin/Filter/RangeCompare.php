<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Query\Condition\BetweenCondition;
use MediaTech\Query\Query\Filter\Filter;
use MediaTech\Query\Query\Filter\FilterGroup;
use MediaTech\Query\Query\Mixin\Filterable;

/**
 * Trait RangeCompare
 * @package MediaTech\Query\Query\Mixin\Filter
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