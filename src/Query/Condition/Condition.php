<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Condition;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Compilable;
use Misantron\QueryBuilder\Helper\Escape;

/**
 * Class Condition.
 */
abstract class Condition implements Compilable
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

    public function __construct(string $column, string $operator = '')
    {
        QueryAssert::validConditionOperator($operator, $this->getAcceptableOperators());

        $this->column = $this->escapeIdentifier($column);
        $this->operator = $operator;
    }

    /**
     * @return array
     */
    abstract protected function getAcceptableOperators(): array;
}
