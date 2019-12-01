<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Condition;

/**
 * Class InCondition.
 */
final class InCondition extends Condition
{
    /**
     * @var array
     */
    private $values;

    public function __construct(string $column, array $values, string $operator)
    {
        parent::__construct($column, $operator);

        $this->values = $this->escapeList($values);
    }

    /**
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
    public function compile(): string
    {
        return sprintf('%s %s (%s)', $this->column, $this->operator, implode(',', $this->values));
    }
}
