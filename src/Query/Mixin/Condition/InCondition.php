<?php

namespace MediaTech\Query\Query\Mixin\Condition;


class InCondition extends Condition
{
    /**
     * @var string
     */
    private $values;

    /**
     * @param string $column
     * @param array $values
     * @param string $operator
     */
    public function __construct(string $column, array $values, string $operator)
    {
        parent::__construct($column, $operator);

        $this->values = $this->escapeList($values);
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $operator
     * @return ValueCondition
     */
    public static function create(string $column, array $values, string $operator)
    {
        return new static($column, $values, $operator);
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return sprintf('%s %s (%s)', $this->column, $this->operator, $this->values);
    }

    /**
     * @return array
     */
    protected function getAcceptableOperators(): array
    {
        return ['IN', 'NOT IN'];
    }
}