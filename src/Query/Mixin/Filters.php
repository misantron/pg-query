<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filter\ArrayCompare;
use Misantron\QueryBuilder\Query\Mixin\Filter\NullCompare;
use Misantron\QueryBuilder\Query\Mixin\Filter\RangeCompare;
use Misantron\QueryBuilder\Query\Mixin\Filter\ValueCompare;

/**
 * Trait Filters.
 */
trait Filters
{
    use ArrayCompare;
    use NullCompare;
    use ValueCompare;
    use RangeCompare;

    /**
     * @var FilterGroup
     */
    private $filters;

    /**
     * @return Filterable
     */
    public function beginGroup(): Filterable
    {
        return $this->andGroup();
    }

    /**
     * @return Filterable
     */
    public function andGroup(): Filterable
    {
        $this->filters->append(Filter::create('(', 'AND', true));

        return $this;
    }

    /**
     * @return Filterable
     */
    public function orGroup(): Filterable
    {
        $this->filters->append(Filter::create('(', 'OR', true));

        return $this;
    }

    /**
     * @return Filterable
     */
    public function endGroup(): Filterable
    {
        $this->filters->append(Filter::create(')'));

        return $this;
    }

    /**
     * @return string
     */
    private function buildFilters(): string
    {
        return $this->filters->notEmpty() ? ' WHERE ' . $this->filters->compile() : '';
    }
}
