<?php

namespace Misantron\QueryBuilder\Query\Condition;

use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Stringable;

/**
 * Class Condition
 * @package Misantron\QueryBuilder\Query\Condition
 */
abstract class Condition implements Stringable
{
    use Escape;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @param string $column
     * @param string $operator
     */
    public function __construct(string $column, string $operator = '')
    {
        $operators = $this->getAcceptableOperators();

        if (!empty($operators) && !in_array($operator, $operators)) {
            throw new \InvalidArgumentException('Invalid condition operator: unexpected value');
        }

        $this->column = $this->escapeIdentifier($column);
        $this->operator = $operator;
    }

    /**
     * @return array
     */
    abstract protected function getAcceptableOperators(): array;
}