<?php

namespace MediaTech\Query\Query\Condition;


/**
 * Class InArrayCondition
 * @package MediaTech\Query\Query\Condition
 */
class InArrayCondition extends Condition
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     */
    public function __construct(string $column, $value, string $operator)
    {
        parent::__construct($column, $operator);

        $this->value = $this->escapeValue($value);
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return InArrayCondition
     */
    public static function create(string $column, $value, string $operator): InArrayCondition
    {
        return new static($column, $value, $operator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptableOperators(): array
    {
        return ['=', '!='];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s %s ANY (%s)', $this->value, $this->operator, $this->column);
    }
}