<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Condition;

/**
 * Class NullCondition.
 */
final class NullCondition extends Condition
{
    /**
     * @return NullCondition
     */
    public static function create(string $column, string $operator): NullCondition
    {
        return new static($column, $operator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptableOperators(): array
    {
        return ['IS', 'IS NOT'];
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        return sprintf('%s %s NULL', $this->column, $this->operator);
    }
}
