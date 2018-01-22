<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Query\Condition\BetweenCondition;

/**
 * Trait RangeCompare
 * @package MediaTech\Query\Query\Mixin\Filter
 *
 * @property FilterGroup $filters
 */
trait RangeCompare
{
    public function between(string $column, array $values)
    {
        return $this->andBetween($column, $values);
    }

    public function andBetween(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(BetweenCondition::create($column, $values), 'AND')
        );
        return $this;
    }

    public function orBetween(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(BetweenCondition::create($column, $values), 'OR')
        );
        return $this;
    }
}