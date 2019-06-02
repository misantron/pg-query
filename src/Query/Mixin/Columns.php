<?php

namespace Misantron\QueryBuilder\Query\Mixin;

use Misantron\QueryBuilder\Assert\QueryAssert;

/**
 * Trait Columns.
 *
 *
 * @method array parseList($items)
 */
trait Columns
{
    /**
     * @var array
     */
    private $columns = [];

    /**
     * @param array|string $items
     *
     * @return Selectable
     */
    public function columns($items): Selectable
    {
        QueryAssert::columnsNotEmpty($items);

        $this->columns = $this->parseList($items);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
