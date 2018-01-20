<?php

namespace MediaTech\Query\Query\Mixin;


use MediaTech\Query\Query\Mixin\Filter\ArrayCompare;
use MediaTech\Query\Query\Mixin\Filter\Filter;
use MediaTech\Query\Query\Mixin\Filter\FilterGroup;
use MediaTech\Query\Query\Mixin\Filter\NullCompare;
use MediaTech\Query\Query\Mixin\Filter\RangeCompare;
use MediaTech\Query\Query\Mixin\Filter\ValueCompare;

/**
 * Trait Filters
 * @package MediaTech\Query\Query\Mixin
 */
trait Filters
{
    use ValueCompare, NullCompare, RangeCompare, ArrayCompare;

    /**
     * @var FilterGroup
     */
    private $filters;

    /**
     * @return Filterable
     */
    public function beginGroup()
    {
        return $this->andGroup();
    }

    /**
     * @return Filterable
     */
    public function andGroup()
    {
        $this->filters->append(Filter::create('(', 'AND', true));

        return $this;
    }

    /**
     * @return Filterable
     */
    public function orGroup()
    {
        $this->filters->append(Filter::create('(', 'OR', true));

        return $this;
    }

    /**
     * @return Filterable
     */
    public function endGroup()
    {
        $this->filters->append(Filter::create(')'));

        return $this;
    }
}