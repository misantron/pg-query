<?php

namespace MediaTech\Query\Query\Mixin\Condition;


/**
 * Class BetweenCondition
 * @package MediaTech\Query\Query\Mixin\Condition
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
        parent::__construct($column, 'BETWEEN');

        $this->values = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);
    }

    /**
     * @param string $column
     * @param array $values
     * @return BetweenCondition
     */
    public static function create(string $column, array $values)
    {
        return new static($column, $values);
    }

    /**
     * @return array
     */
    protected function getAcceptableOperators(): array
    {
        return ['BETWEEN'];
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $values = $this->values;
        list($rangeBegin, $rangeEnd) = $values;

        return sprintf('%s %s %s AND %s', $this->column, $this->operator, $rangeBegin, $rangeEnd);
    }
}