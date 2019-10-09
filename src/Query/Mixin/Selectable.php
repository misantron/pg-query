<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Interface Selectable.
 */
interface Selectable
{
    /**
     * @param array|string $items
     *
     * @return Selectable
     */
    public function columns($items): Selectable;
}
