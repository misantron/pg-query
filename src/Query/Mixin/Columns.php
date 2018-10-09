<?php

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Trait Columns
 * @package Misantron\QueryBuilder\Query\Mixin
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

    /**
     * @param array|string $items
     */
    private function assertColumnsEmpty($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }
    }
}