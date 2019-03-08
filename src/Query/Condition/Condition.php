<?php

namespace Misantron\QueryBuilder\Query\Condition;

use Misantron\QueryBuilder\Assert\Assert;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Stringable;

/**
 * Class Condition.
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
        Assert::validConditionOperator($operator, $this->getAcceptableOperators());

        $this->column = $this->escapeIdentifier($column);
        $this->operator = $operator;
    }

    /**
     * @return array
     */
    abstract protected function getAcceptableOperators(): array;
}
