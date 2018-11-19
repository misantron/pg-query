<?php

namespace Misantron\QueryBuilder\Query\Mixin;

/**
 * Trait Returning.
 *
 *
 * @method assertColumnsEmpty($items)
 */
trait Returning
{
    /**
     * @var array
     */
    private $returning = [];

    public function returning($items)
    {
        $this->assertColumnsEmpty($items);

        $this->returning = $this->parseList($items);

        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    private function assertReturningSet()
    {
        if (empty($this->returning)) {
            throw new \RuntimeException('Data fetch error: returning fields must be set');
        }
    }

    /**
     * @return string
     */
    private function buildReturning(): string
    {
        return !empty($this->returning) ? ' RETURNING ' . implode(',', $this->returning) : '';
    }
}
