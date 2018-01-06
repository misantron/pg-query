<?php

namespace MediaTech\Query\Query\Mixin;


/**
 * Trait Conditions
 * @package MediaTech\Query\Query\Mixin
 *
 * @method string escapeIdentifier(string $value, bool $quote = true)
 * @method string escapeValue(string $value)
 */
trait Conditions
{
    /**
     * @var array
     */
    private $conditions;

    public function beginGroup()
    {
        return $this->andGroup();
    }

    /**
     * @return Conditions
     */
    public function andGroup()
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => '(',
            'group' => true,
        ];

        return $this;
    }

    public function orGroup()
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => '(',
            'group' => true,
        ];

        return $this;
    }

    public function endGroup()
    {
        $this->conditions[] = [
            'sign' => null,
            'condition' => ')'
        ];

        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return Conditions
     */
    public function equals(string $column, $value)
    {
        return $this->andEquals($column, $value);
    }

    public function andEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '='),
        ];
        return $this;
    }

    public function orEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '='),
        ];
        return $this;
    }

    public function notEquals(string $column, $value)
    {
        return $this->andNotEquals($column, $value);
    }

    public function andNotEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '!='),
        ];
        return $this;
    }

    public function orNotEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '!='),
        ];
        return $this;
    }

    public function isNull()
    {

    }

    public function andIsNull()
    {

    }

    public function orIsNull()
    {

    }

    public function isNotNull()
    {

    }

    public function andIsNotNull()
    {

    }

    public function orIsNotNull()
    {

    }

    public function in()
    {

    }

    public function andIn()
    {

    }

    public function orIn()
    {

    }

    public function notIn()
    {

    }

    public function andNotIn()
    {

    }

    public function orNotIn()
    {

    }

    public function inArray()
    {

    }

    public function andInArray()
    {

    }

    public function orInArray()
    {

    }

    public function notInArray()
    {

    }

    public function andNotInArray()
    {

    }

    public function orNotInArray()
    {

    }

    private function buildEquals(string $column, $value, string $operator)
    {
        return sprintf('%s %s %s', $this->escapeIdentifier($column, false), $operator, $this->escapeValue($value));
    }

    private function buildConditions(): string
    {
        $trimSign = true;

        return (string)array_reduce($this->conditions, function (string $query, array $part) use (&$trimSign) {
            $condition = (string)$part['condition'];
            if (!$trimSign && !empty($part['sign'])) {
                $condition = $part['sign'] . ' ' . $condition;
            }
            $trimSign = false;
            if (!empty($part['group'])) {
                $trimSign = true;
            }
            return trim($query . ' ' . $condition);
        }, '');
    }
}