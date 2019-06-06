<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Condition;

use Misantron\QueryBuilder\Assert\QueryAssert;

/**
 * Class InArrayCondition.
 */
final class InArrayCondition extends Condition
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     */
    public function __construct(string $column, $value, string $operator)
    {
        parent::__construct($column, $operator);

        QueryAssert::valueIsScalar($value);

        $this->value = $this->escapeValue($value);
    }

    /**
     * @param string $column
     * @param mixed  $value
     * @param string $operator
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
    public function compile(): string
    {
        return sprintf('%s %s ANY(%s)', $this->value, $this->operator, $this->column);
    }
}
