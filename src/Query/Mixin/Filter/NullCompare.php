<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Query\Mixin\Condition\NullCondition;

/**
 * Trait NullCompare
 * @package MediaTech\Query\Query\Mixin\Filter
 *
 * @property FilterGroup $filters
 */
trait NullCompare
{
    public function isNull(string $column)
    {
        return $this->andIsNull($column);
    }

    public function andIsNull(string $column)
    {
        $this->filters->append(
            Filter::create(
                NullCondition::create($column, 'IS'), 'AND'
            )
        );
        return $this;
    }

    public function orIsNull(string $column)
    {
        $this->filters->append(
            Filter::create(
                NullCondition::create($column, 'IS'), 'OR'
            )
        );
        return $this;
    }

    public function isNotNull(string $column)
    {
        return $this->andIsNotNull($column);
    }

    public function andIsNotNull(string $column)
    {
        $this->filters->append(
            Filter::create(
                NullCondition::create($column, 'IS NOT'), 'AND'
            )
        );
        return $this;
    }

    public function orIsNotNull(string $column)
    {
        $this->filters->append(
            Filter::create(
                NullCondition::create($column, 'IS NOT'), 'OR'
            )
        );
        return $this;
    }
}