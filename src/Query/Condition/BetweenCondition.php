<?php

namespace MediaTech\Query\Query\Condition;


/**
 * Class BetweenCondition
 * @package MediaTech\Query\Query\Condition
 */
class BetweenCondition extends Condition
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param string $column
     * @param array $values
     */
    public function __construct(string $column, array $values)
    {
        parent::__construct($column);

        $this->values = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        if (sizeof($this->values) !== 2) {
            throw new \InvalidArgumentException('Invalid value: array must contains two elements - begin and end of period');
        }
    }

    /**
     * @param string $column
     * @param array $values
     * @return BetweenCondition
     */
    public static function create(string $column, array $values): BetweenCondition
    {
        return new static($column, $values);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptableOperators(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $values = $this->values;
        list($rangeBegin, $rangeEnd) = $values;

        return sprintf('%s BETWEEN %s AND %s', $this->column, $rangeBegin, $rangeEnd);
    }
}