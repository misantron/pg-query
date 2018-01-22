<?php

namespace MediaTech\Query\Query\Condition;


use MediaTech\Query\Helper\Escape;
use MediaTech\Query\Stringable;

/**
 * Class Condition
 * @package MediaTech\Query\Query\Condition
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