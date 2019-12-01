<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Condition;

/**
 * Class ValueCondition.
 */
final class ValueCondition extends Condition
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $column, $value, string $operator)
    {
        parent::__construct($column, $operator);

        $this->value = $this->escapeValue($value);
    }

    /**
     * @param mixed $value
     */
    public static function create(string $column, $value, string $operator): ValueCondition
    {
        return new static($column, $value, $operator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptableOperators(): array
    {
        return ['=', '!=', '>', '<', '>=', '<='];
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        return sprintf('%s %s %s', $this->column, $this->operator, $this->value);
    }
}
