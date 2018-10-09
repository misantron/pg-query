<?php

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Selectable
 * @package Misantron\QueryBuilder\Query\Mixin
 */
interface Selectable
{
    /**
     * @param array|string $items
     * @return Selectable
     */
    public function columns($items);
}