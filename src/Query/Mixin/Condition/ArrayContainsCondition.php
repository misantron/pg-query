<?php

namespace MediaTech\Query\Query\Mixin\Condition;


/**
 * Class ArrayContainsCondition
 * @package MediaTech\Query\Query\Mixin\Condition
 */
class ArrayContainsCondition extends Condition
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param string $column
     * @param array $value
     */
    public function __construct(string $column, array $values)
    {
        parent::__construct($column, '@>');

        $this->values = $this->escapeArray($values);
    }

    /**
     * @param string $column
     * @param array $value
     * @return ArrayContainsCondition
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
        return ['@>'];
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return sprintf('%s %s %s', $this->column, $this->operator, $this->values);
    }
}