<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Columns;
use Misantron\QueryBuilder\Query\Mixin\DataFetching;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Retrievable;
use Misantron\QueryBuilder\Query\Mixin\Selectable;
use Misantron\QueryBuilder\Server;

/**
 * Class Select.
 *
 *
 * @method Select table(string $name)
 * @method Select columns($items)
 * @method Select execute()
 * @method Select beginGroup()
 * @method Select andGroup()
 * @method Select orGroup()
 * @method Select endGroup()
 * @method Select equals(string $column, $value)
 * @method Select andEquals(string $column, $value)
 * @method Select orEquals(string $column, $value)
 * @method Select notEquals(string $column, $value)
 * @method Select andNotEquals(string $column, $value)
 * @method Select orNotEquals(string $column, $value)
 * @method Select more(string $column, $value)
 * @method Select andMore(string $column, $value)
 * @method Select orMore(string $column, $value)
 * @method Select moreOrEquals(string $column, $value)
 * @method Select andMoreOrEquals(string $column, $value)
 * @method Select orMoreOrEquals(string $column, $value)
 * @method Select less(string $column, $value)
 * @method Select andLess(string $column, $value)
 * @method Select orLess(string $column, $value)
 * @method Select lessOrEquals(string $column, $value)
 * @method Select andLessOrEquals(string $column, $value)
 * @method Select orLessOrEquals(string $column, $value)
 * @method Select between(string $column, array $values)
 * @method Select andBetween(string $column, array $values)
 * @method Select orBetween(string $column, array $values)
 * @method Select in(string $column, array $values)
 * @method Select andIn(string $column, array $values)
 * @method Select orIn(string $column, array $values)
 * @method Select notIn(string $column, array $values)
 * @method Select andNotIn(string $column, array $values)
 * @method Select orNotIn(string $column, array $values)
 * @method Select inArray(string $column, $value)
 * @method Select andInArray(string $column, $value)
 * @method Select orInArray(string $column, $value)
 * @method Select notInArray(string $column, $value)
 * @method Select andNotInArray(string $column, $value)
 * @method Select orNotInArray(string $column, $value)
 * @method Select arrayContains(string $column, array $values)
 * @method Select andArrayContains(string $column, array $values)
 * @method Select orArrayContains(string $column, array $values)
 * @method Select isNull(string $column)
 * @method Select andIsNull(string $column)
 * @method Select orIsNull(string $column)
 * @method Select isNotNull(string $column)
 * @method Select andIsNotNull(string $column)
 * @method Select orIsNotNull(string $column)
 */
class Select extends Query implements Selectable, Filterable, Retrievable
{
    use Columns, Filters, DataFetching;

    private const DEFAULT_TABLE_ALIAS = 't1';

    /**
     * @var string
     */
    private $alias;

    /**
     * @var bool
     */
    private $distinct;

    /**
     * @var array
     */
    private $joins = [];

    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var string
     */
    private $having;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var Select[]
     */
    private $with = [];

    /**
     * @param Server $server
     * @param string $table
     * @param string $alias
     */
    public function __construct(Server $server, string $table, string $alias = self::DEFAULT_TABLE_ALIAS)
    {
        parent::__construct($server);

        $this->table($table);
        $this->alias = $this->escapeIdentifier($alias);
        $this->filters = new FilterGroup();
    }

    /**
     * @param string $value
     *
     * @return Select
     */
    public function alias(string $value): Select
    {
        $this->alias = $this->escapeIdentifier($value);

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return Select
     */
    public function distinct(bool $value = true): Select
    {
        $this->distinct = $value;

        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     *
     * @return Select
     */
    public function innerJoin(string $table, string $alias, string $condition): Select
    {
        $this->appendJoin('inner', $table, $alias, $condition);

        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     *
     * @return Select
     */
    public function leftJoin(string $table, string $alias, string $condition): Select
    {
        $this->appendJoin('left', $table, $alias, $condition);

        return $this;
    }

    /**
     * @param string $type
     * @param string $table
     * @param string $alias
     * @param string $condition
     */
    private function appendJoin(string $type, string $table, string $alias, string $condition): void
    {
        $hash = $this->makeHash($type, $table, $alias);

        QueryAssert::tableJoinPossible($this->joins, $alias, $hash);

        $this->joins[$hash] = [
            'type' => $type,
            'table' => $table,
            'alias' => $alias,
            'condition' => $condition,
        ];
    }

    /**
     * @param string $type
     * @param string $table
     * @param string $alias
     *
     * @return string
     */
    private function makeHash(string $type, string $table, string $alias): string
    {
        $table = $this->escapeIdentifier($table);
        $alias = $this->escapeIdentifier($alias);

        return sha1($type . '_' . $table . '_' . $alias);
    }

    /**
     * @param Select[] $values
     *
     * @return Select
     */
    public function with(array $values): Select
    {
        foreach ($values as $alias => $value) {
            QueryAssert::valueIsSelectQuery($value);

            $this->with[$this->escapeIdentifier($alias)] = $value;
        }

        return $this;
    }

    /**
     * @param array|string $values
     *
     * @return Select
     */
    public function groupBy($values): Select
    {
        $this->groupBy = $this->parseList($values);

        return $this;
    }

    /**
     * @param array $values
     *
     * @return Select
     */
    public function orderBy(array $values): Select
    {
        $this->orderBy = $values;

        return $this;
    }

    /**
     * @param string $condition
     *
     * @return Select
     */
    public function having(string $condition): Select
    {
        $this->having = $condition;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Select
     */
    public function limit(int $value): Select
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return Select
     */
    public function offset(int $value): Select
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return Select
     */
    public function range(int $offset, int $limit): Select
    {
        $this->offset = $offset;
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function rowsCount(): int
    {
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        if (!empty($this->having) && empty($this->groupBy)) {
            throw QueryRuntimeException::havingWithoutGroup();
        }

        $query = '';
        $query .= $this->buildWith();
        $query .= $this->buildSelect();
        $query .= $this->buildJoins();
        $query .= $this->buildFilters();
        $query .= $this->buildGroupBy();
        $query .= $this->buildHaving();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimitOffset();

        return $query;
    }

    /**
     * @return string
     */
    private function buildWith(): string
    {
        $queries = [];
        foreach ($this->with as $alias => $query) {
            $queries[] = $alias . ' AS (' . $query->compile() . ')';
        }

        return !empty($queries) ? 'WITH ' . implode(', ', $queries) . ' ' : '';
    }

    /**
     * @return string
     */
    private function buildSelect(): string
    {
        $columns = empty($this->columns) ? '*' : implode(',', $this->columns);

        $str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '');
        $str .= $columns . ' FROM ' . $this->table . ' ' . $this->alias;

        return $str;
    }

    /**
     * @return string
     */
    private function buildJoins(): string
    {
        $joins = [];
        foreach ($this->joins as $join) {
            $joins[] = sprintf(
                '%s JOIN %s %s ON %s',
                strtoupper($join['type']),
                $join['table'],
                $join['alias'],
                $join['condition']
            );
        }

        return !empty($joins) ? ' ' . implode(' ', $joins) : '';
    }

    /**
     * @return string
     */
    private function buildGroupBy(): string
    {
        return !empty($this->groupBy) ? ' GROUP BY ' . implode(',', $this->groupBy) : '';
    }

    /**
     * @return string
     */
    private function buildHaving(): string
    {
        return !empty($this->having) ? ' HAVING ' . $this->having : '';
    }

    /**
     * @return string
     */
    private function buildOrderBy(): string
    {
        return !empty($this->orderBy) ? ' ORDER BY ' . implode(',', $this->orderBy) : '';
    }

    /**
     * @return string
     */
    private function buildLimitOffset(): string
    {
        $str = '';

        if ($this->limit !== null) {
            $str .= ' LIMIT ' . $this->limit;
        }
        if ($this->offset !== null) {
            $str .= ' OFFSET ' . $this->offset;
        }

        return $str;
    }
}
