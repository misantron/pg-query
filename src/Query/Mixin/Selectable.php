<?php

namespace MediaTech\Query\Query\Mixin;


interface Selectable
{
    /**
     * @param array|string $items
     * @return Selectable
     */
    public function columns($items);
}