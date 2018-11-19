<?php

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Trait Columns.
 *
 *
 * @method array parseList($items)
 * @method       assertColumnsEmpty($items)
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
    public function columns($items)
    {
        $this->assertColumnsEmpty($items);

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
