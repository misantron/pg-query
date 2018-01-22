<?php

namespace MediaTech\Query\Query\Mixin;


/**
 * Trait Columns
 * @package MediaTech\Query\Query\Mixin
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
     * @param array|string $items
     */
    private function assertColumnsEmpty($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }
    }
}