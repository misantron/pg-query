<?php

namespace MediaTech\Query\Query\Mixin;


/**
 * Trait Conditions
 * @package MediaTech\Query\Query\Mixin
 *
 * @method string escapeIdentifier(string $value, bool $quote = true)
 * @method string escapeValue(string $value)
 * @method string escapeArray(array $values)
 * @method bool isIntegerArray(array $array)
 */
trait Conditions
{
    /**
     * @var array
     */
    private $conditions = [];

    /**
     * @return Filterable
     */
    public function beginGroup()
    {
        return $this->andGroup();
    }

    /**
     * @return Filterable
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

    /**
     * @return Filterable
     */
    public function orGroup()
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => '(',
            'group' => true,
        ];

        return $this;
    }

    /**
     * @return Filterable
     */
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
     *
     * @return Filterable
     */
    public function equals(string $column, $value)
    {
        return $this->andEquals($column, $value);
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '='),
        ];
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
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

    public function more(string $column, $value)
    {
        return $this->andMore($column, $value);
    }

    public function andMore(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '>'),
        ];
        return $this;
    }

    public function orMore(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '>'),
        ];
        return $this;
    }

    public function moreOrEquals(string $column, $value)
    {
        return $this->andMoreOrEquals($column, $value);
    }

    public function andMoreOrEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '>='),
        ];
        return $this;
    }

    public function orMoreOrEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '>='),
        ];
        return $this;
    }

    public function less(string $column, $value)
    {
        return $this->andLess($column, $value);
    }

    public function andLess(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '<'),
        ];
        return $this;
    }

    public function orLess(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '<'),
        ];
        return $this;
    }

    public function lessOrEquals(string $column, $value)
    {
        return $this->andLessOrEquals($column, $value);
    }

    public function andLessOrEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildEquals($column, $value, '<='),
        ];
        return $this;
    }

    public function orLessOrEquals(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildEquals($column, $value, '<='),
        ];
        return $this;
    }

    public function between(string $column, array $values)
    {
        return $this->andBetween($column, $values);
    }

    public function andBetween(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildBetween($column, $values),
        ];
        return $this;
    }

    public function orBetween(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildBetween($column, $values),
        ];
        return $this;
    }

    public function isNull(string $column)
    {
        return $this->andIsNull($column);
    }

    public function andIsNull(string $column)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildNull($column, 'IS'),
        ];
        return $this;
    }

    public function orIsNull(string $column)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildNull($column, 'IS'),
        ];
        return $this;
    }

    public function isNotNull(string $column)
    {
        return $this->andIsNotNull($column);
    }

    public function andIsNotNull(string $column)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildNull($column, 'IS NOT'),
        ];
        return $this;
    }

    public function orIsNotNull(string $column)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildNull($column, 'IS NOT'),
        ];
        return $this;
    }

    public function in(string $column, array $values)
    {
        return $this->andIn($column, $values);
    }

    public function andIn(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildIn($column, $values, 'IN'),
        ];
        return $this;
    }

    public function orIn(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildIn($column, $values, 'IN'),
        ];
        return $this;
    }

    public function notIn(string $column, array $values)
    {
        return $this->andNotIn($column, $values);
    }

    public function andNotIn(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildIn($column, $values, 'NOT IN'),
        ];
        return $this;
    }

    public function orNotIn(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildIn($column, $values, 'NOT IN'),
        ];
        return $this;
    }

    public function inArray(string $column, $value)
    {
        return $this->andInArray($column, $value);
    }

    public function andInArray(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildInArray($column, $value, '='),
        ];
        return $this;
    }

    public function orInArray(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildInArray($column, $value, '='),
        ];
        return $this;
    }

    public function notInArray(string $column, $value)
    {
        return $this->andNotInArray($column, $value);
    }

    public function andNotInArray(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildInArray($column, $value, '!='),
        ];
        return $this;
    }

    public function orNotInArray(string $column, $value)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildInArray($column, $value, '!='),
        ];
        return $this;
    }

    public function arrayContains(string $column, array $values)
    {
        return $this->andArrayContains($column, $values);
    }

    public function andArrayContains(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'AND',
            'condition' => $this->buildArrayContains($column, $values),
        ];
        return $this;
    }

    public function orArrayContains(string $column, array $values)
    {
        $this->conditions[] = [
            'sign' => 'OR',
            'condition' => $this->buildArrayContains($column, $values),
        ];
        return $this;
    }

    private function buildEquals(string $column, $value, string $operator): string
    {
        return sprintf('%s %s %s', $this->escapeIdentifier($column, false), $operator, $this->escapeValue($value));
    }

    private function buildBetween(string $column, array $values): string
    {
        list($rangeBegin, $rangeEnd) = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        return sprintf('%s BETWEEN %s AND %s', $this->escapeIdentifier($column, false), $rangeBegin, $rangeEnd);
    }

    private function buildNull(string $column, string $operator): string
    {
        return sprintf('%s %s NULL', $this->escapeIdentifier($column, false), $operator);
    }

    private function buildIn(string $column, array $values, string $operator): string
    {
        return sprintf('%s %s (%s)', $this->escapeIdentifier($column, false), $operator, $this->escapeList($values));
    }

    private function buildInArray(string $column, $value, string $operator): string
    {
        return sprintf('%s %s ANY (%s)', $this->escapeValue($value), $operator, $this->escapeIdentifier($column, false));
    }

    private function buildArrayContains(string $column, array $values): string
    {
        return sprintf('%s @> %s', $this->escapeIdentifier($column, false), $this->escapeArray($values));
    }

    /**
     * @param array $items
     * @return string
     */
    private function escapeList(array $items): string
    {
        $type = $this->isIntegerArray($items) ? 'integer': 'string';
        $filtered = array_filter($items);
        if (empty($filtered)) {
            throw new \InvalidArgumentException('Value list is empty');
        }
        if ($type === 'string') {
            $filtered = array_map(function (string $item) {
                return "'" . str_replace("'", "''", $item) . "'";
            }, $filtered);
        }
        return implode(',', $filtered);
    }

    /**
     * @return string
     */
    private function buildConditions(): string
    {
        $trimSign = true;
        $conditions = $this->conditions;

        $query = (string)array_reduce(
            $conditions,
            function (string $query, array $part) use (&$trimSign) {
                $condition = (string)$part['condition'];
                if (!$trimSign && !empty($part['sign'])) {
                    $condition = $part['sign'] . ' ' . $condition;
                }
                $trimSign = false;
                if (!empty($part['group'])) {
                    $trimSign = true;
                }
                return trim($query . ' ' . $condition);
            },
            ''
        );

        return $query === '' ? $query : ' WHERE ' . $query;
    }
}