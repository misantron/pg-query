<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin\Filter;

use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;

/**
 * Trait NullCompare.
 *
 * @property FilterGroup $filters
 */
trait NullCompare
{
    /**
     * @return Filterable
     */
    public function isNull(string $column): Filterable
    {
        return $this->andIsNull($column);
    }

    /**
     * @return Filterable
     */
    public function andIsNull(string $column): Filterable
    {
        $this->filters->append(
            Filter::create(NullCondition::create($column, 'IS'), 'AND')
        );

        return $this;
    }

    /**
     * @return Filterable
     */
    public function orIsNull(string $column): Filterable
    {
        $this->filters->append(
            Filter::create(NullCondition::create($column, 'IS'), 'OR')
        );

        return $this;
    }

    /**
     * @return Filterable
     */
    public function isNotNull(string $column): Filterable
    {
        return $this->andIsNotNull($column);
    }

    /**
     * @return Filterable
     */
    public function andIsNotNull(string $column): Filterable
    {
        $this->filters->append(
            Filter::create(NullCondition::create($column, 'IS NOT'), 'AND')
        );

        return $this;
    }

    /**
     * @return Filterable
     */
    public function orIsNotNull(string $column): Filterable
    {
        $this->filters->append(
            Filter::create(NullCondition::create($column, 'IS NOT'), 'OR')
        );

        return $this;
    }
}
