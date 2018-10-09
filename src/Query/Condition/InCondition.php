<?php

namespace Misantron\QueryBuilder\Query\Condition;

/**
 * Class InCondition
 * @package Misantron\QueryBuilder\Query\Condition
 */
class InCondition extends Condition
{
    /**
     * @var array
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
     * @return InCondition
     */
    public static function create(string $column, array $values, string $operator): InCondition
    {
        return new static($column, $values, $operator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptableOperators(): array
    {
        return ['IN', 'NOT IN'];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s %s (%s)', $this->column, $this->operator, implode(',', $this->values));
    }
}