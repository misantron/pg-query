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

    public function beginGroup(): Filterable
    {
        return $this->andGroup();
    }

    public function andGroup(): Filterable
    {
        $this->filters->append(Filter::create('(', 'AND', true));

        return $this;
    }

    public function orGroup(): Filterable
    {
        $this->filters->append(Filter::create('(', 'OR', true));

        return $this;
    }

    public function endGroup(): Filterable
    {
        $this->filters->append(Filter::create(')'));

        return $this;
    }

    private function buildFilters(): string
    {
        return $this->filters->notEmpty() ? ' WHERE ' . $this->filters->compile() : '';
    }
}
