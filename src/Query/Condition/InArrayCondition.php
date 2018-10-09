<?php

namespace Misantron\QueryBuilder\Query\Condition;

/**
 * Class InArrayCondition.
 */
class InArrayCondition extends Condition
{
    /**
     * @var string|int|float|bool
     */
    private $value;

    /**
     * @param string                $column
     * @param string|int|float|bool $value
     * @param string                $operator
     */
    public function __construct(string $column, $value, string $operator)
    {
        parent::__construct($column, $operator);

        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('Invalid value: value must be a scalar');
        }

        $this->value = $this->escapeValue($value);
    }

    /**
     * @param string                $column
     * @param string|int|float|bool $value
     * @param string                $operator
     *
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
        return sprintf('%s %s ANY(%s)', $this->value, $this->operator, $this->column);
    }
}
