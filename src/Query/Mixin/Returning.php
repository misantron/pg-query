<?php

namespace Misantron\QueryBuilder\Query\Mixin;

use Misantron\QueryBuilder\Assert\Assert;

/**
 * Trait Returning.
 *
 *
 * @method array parseList($items)
 */
trait Returning
{
    /**
     * @var array
     */
    private $returning = [];

    public function returning($items)
    {
        Assert::columnsNotEmpty($items);

        $this->returning = $this->parseList($items);

        return $this;
    }

    /**
     * @return string
     */
    private function buildReturning(): string
    {
        return !empty($this->returning) ? ' RETURNING ' . implode(',', $this->returning) : '';
    }
}
