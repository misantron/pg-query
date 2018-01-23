<?php

namespace MediaTech\Query\Query\Condition;


/**
 * Class NullCondition
 * @package MediaTech\Query\Query\Condition
 */
class NullCondition extends Condition
{
    /**
     * @param string $column
     * @param string $operator
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
    public function __toString(): string
    {
        return sprintf('%s %s NULL', $this->column, $this->operator);
    }
}