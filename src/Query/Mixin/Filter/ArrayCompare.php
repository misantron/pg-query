<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Query\Condition\ArrayContainsCondition;
use MediaTech\Query\Query\Condition\InArrayCondition;
use MediaTech\Query\Query\Condition\InCondition;

/**
 * Trait ArrayCompare
 * @package MediaTech\Query\Query\Mixin\Filter
 *
 * @property FilterGroup $filters
 */
trait ArrayCompare
{
    public function in(string $column, array $values)
    {
        return $this->andIn($column, $values);
    }

    public function andIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'AND')
        );
        return $this;
    }

    public function orIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'OR')
        );
        return $this;
    }

    public function notIn(string $column, array $values)
    {
        return $this->andNotIn($column, $values);
    }

    public function andNotIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'AND')
        );
        return $this;
    }

    public function orNotIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'OR')
        );
        return $this;
    }

    public function inArray(string $column, $value)
    {
        return $this->andInArray($column, $value);
    }

    public function andInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'AND')
        );
        return $this;
    }

    public function orInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'OR')
        );
        return $this;
    }

    public function notInArray(string $column, $value)
    {
        return $this->andNotInArray($column, $value);
    }

    public function andNotInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'AND')
        );
        return $this;
    }

    public function orNotInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'OR')
        );
        return $this;
    }

    public function arrayContains(string $column, array $values)
    {
        return $this->andArrayContains($column, $values);
    }

    public function andArrayContains(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'AND')
        );
        return $this;
    }

    public function orArrayContains(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'OR')
        );
        return $this;
    }
}