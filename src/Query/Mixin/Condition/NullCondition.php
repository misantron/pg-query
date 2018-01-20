<?php

namespace MediaTech\Query\Query\Mixin\Condition;


/**
 * Class NullCondition
 * @package MediaTech\Query\Query\Mixin\Condition
 */
class NullCondition extends Condition
{
    /**
     * @param string $column
     * @param string $operator
     * @return NullCondition
     */
    public static function create(string $column, string $operator)
    {
        return new static($column, $operator);
    }

    protected function getAcceptableOperators(): array
    {
        return ['IS', 'NOT IS'];
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return sprintf('%s %s NULL', $this->column, $this->operator);
    }
}