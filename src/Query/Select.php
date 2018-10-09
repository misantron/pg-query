<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Columns;
use Misantron\QueryBuilder\Query\Mixin\DataFetching;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Retrievable;
use Misantron\QueryBuilder\Query\Mixin\Selectable;

/**
 * Class Select.
 *
 *
 * @method Select execute()
 * @method Select columns($items)
 */
class Select extends Query implements Selectable, Filterable, Retrievable
{
    use Columns, Filters, DataFetching;

    const DEFAULT_TABLE_ALIAS = 't1';

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
     * @param \PDO   $pdo
     * @param string $table
     * @param string $alias
     */
    public function __construct(\PDO $pdo, string $table, string $alias = self::DEFAULT_TABLE_ALIAS)
    {
        parent::__construct($pdo, $table);

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
    public function join(string $table, string $alias, string $condition): Select
    {
        return $this->innerJoin($table, $alias, $condition);
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
    private function appendJoin(string $type, string $table, string $alias, string $condition)
    {
        $hash = $this->getHash($table, $alias);

        $this->assertAliasInUse($alias);

        $this->joins[$hash] = [
            'type' => $type,
            'table' => $table,
            'alias' => $alias,
            'condition' => $condition,
        ];
    }

    /**
     * @param string $table
     * @param string $alias
     *
     * @return string
     */
    private function getHash(string &$table, string &$alias): string
    {
        $table = $this->escapeIdentifier($table);
        $alias = $this->escapeIdentifier($alias);

        $hash = hash('crc32', $table . '_' . $alias);

        if (isset($this->joins[$hash])) {
            throw new \InvalidArgumentException('Table has already joined');
        }

        return $hash;
    }

    /**
     * @param string $alias
     */
    private function assertAliasInUse(string $alias)
    {
        foreach ($this->joins as $join) {
            if ($alias === $join['alias']) {
                throw new \InvalidArgumentException('Alias is already in use');
            }
        }
    }

    /**
     * @param Select[] $values
     *
     * @return Select
     */
    public function with(array $values): Select
    {
        foreach ($values as $alias => $value) {
            $alias = $this->escapeIdentifier($alias);
            if (!$value instanceof Select) {
                throw new \InvalidArgumentException('Only select query can be used');
            }
            // alias name cannot be duplicated
            $this->with[$alias] = $value;
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
        $this->assertQueryExecuted();

        return $this->statement->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $this->validateQuery();

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
     * @throws \RuntimeException
     */
    private function validateQuery()
    {
        if (!empty($this->having) && empty($this->groupBy)) {
            throw new \RuntimeException('Query build error: using having without group by');
        }
    }

    /**
     * @return string
     */
    private function buildWith(): string
    {
        $queries = [];
        foreach ($this->with as $alias => $query) {
            $queries[] = $alias . ' AS (' . (string)$query . ')';
        }

        return !empty($queries) ? 'WITH ' . implode(', ', $queries) . ' ' : '';
    }

    private function buildSelect(): string
    {
        $str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '');
        $str .= (empty($this->columns)
                ? '*'
                : implode(',', $this->columns)) . ' FROM ' . $this->table . ' ' . $this->alias;

        return $str;
    }

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

    private function buildGroupBy(): string
    {
        return !empty($this->groupBy) ? ' GROUP BY ' . implode(',', $this->groupBy) : '';
    }

    private function buildHaving(): string
    {
        return !empty($this->having) ? ' HAVING ' . $this->having : '';
    }

    private function buildOrderBy(): string
    {
        return !empty($this->orderBy) ? ' ORDER BY ' . implode(',', $this->orderBy) : '';
    }

    private function buildLimitOffset(): string
    {
        $str = '';

        if (is_int($this->limit)) {
            $str .= ' LIMIT ' . $this->limit;
        }
        if (is_int($this->offset)) {
            $str .= ' OFFSET ' . $this->offset;
        }

        return $str;
    }
}
