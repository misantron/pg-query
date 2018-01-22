<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Query\Condition\ArrayContainsCondition;
use MediaTech\Query\Query\Condition\InArrayCondition;
use MediaTech\Query\Query\Condition\InCondition;
use MediaTech\Query\Query\Filter\Filter;
use MediaTech\Query\Query\Filter\FilterGroup;
use MediaTech\Query\Query\Mixin\Filterable;

/**
 * Trait ArrayCompare
 * @package MediaTech\Query\Query\Mixin\Filter
 *
 * @property FilterGroup $filters
 */
trait ArrayCompare
{
    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function in(string $column, array $values)
    {
        return $this->andIn($column, $values);
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function andIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function orIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'IN'), 'OR')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function notIn(string $column, array $values)
    {
        return $this->andNotIn($column, $values);
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function andNotIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function orNotIn(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(InCondition::create($column, $values, 'NOT IN'), 'OR')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function inArray(string $column, $value)
    {
        return $this->andInArray($column, $value);
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '='), 'OR')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function notInArray(string $column, $value)
    {
        return $this->andNotInArray($column, $value);
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andNotInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orNotInArray(string $column, $value)
    {
        $this->filters->append(
            Filter::create(InArrayCondition::create($column, $value, '!='), 'OR')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function arrayContains(string $column, array $values)
    {
        return $this->andArrayContains($column, $values);
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function andArrayContains(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'AND')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return Filterable
     */
    public function orArrayContains(string $column, array $values)
    {
        $this->filters->append(
            Filter::create(ArrayContainsCondition::create($column, $values), 'OR')
        );
        return $this;
    }
}