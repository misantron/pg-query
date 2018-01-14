<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;
use MediaTech\Query\Query\Select\FetchMode;

class Select extends Query
{
    use Conditions;

    const DEFAULT_TABLE_ALIAS = 't1';

    /**
     * @var string
     */
    private $alias;

    /**
     * @var array
     */
    private $columns = [];

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
     * @var int
     */
    private $fetchMode;

    /**
     * @var Select[]
     */
    private $with = [];

    /**
     * @param \PDO $pdo
     * @param string $table
     * @param string $alias
     * @param int $fetchMode
     */
    public function __construct(
        \PDO $pdo,
        string $table,
        string $alias = self::DEFAULT_TABLE_ALIAS,
        int $fetchMode = FetchMode::ASSOC
    ) {
        parent::__construct($pdo, $table);

        $this->alias = $this->escapeIdentifier($alias, false);
        $this->fetchMode = $fetchMode;
    }

    /**
     * @param string $value
     * @return Select
     */
    public function alias(string $value): Select
    {
        $this->alias = $this->escapeIdentifier($value, false);

        return $this;
    }

    /**
     * @param array $items
     * @return Select
     */
    public function columns(array $items): Select
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }

        $this->columns = $this->parseList($items);

        return $this;
    }

    /**
     * @return Select
     */
    public function distinct(): Select
    {
        $this->distinct = true;

        return $this;
    }

    /**
     * @param int $value
     * @return Select
     */
    public function fetchMode(int $value): Select
    {
        if (!in_array($value, FetchMode::getKeys())) {
            throw new \InvalidArgumentException('Invalid fetch mode');
        }

        $this->fetchMode = $value;

        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
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
     * @return Select
     */
    public function innerJoin(string $table, string $alias, string $condition): Select
    {
        $table = $this->escapeIdentifier($table, false);
        $alias = $this->escapeIdentifier($alias, false);

        $hash = $this->validateJoin($table, $alias);

        $this->joins[$hash] = [
            'type' => 'inner',
            'table' => $table,
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Select
     */
    public function leftJoin(string $table, string $alias, string $condition): Select
    {
        $table = $this->escapeIdentifier($table, false);
        $alias = $this->escapeIdentifier($alias, false);

        $hash = $this->validateJoin($table, $alias);

        $this->joins[$hash] = [
            'type' => 'left',
            'table' => $table,
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @return string
     */
    private function validateJoin(string $table, string $alias): string
    {
        $hash = hash('crc32', $table . '_' . $alias);

        if (isset($this->joins[$hash])) {
            throw new \InvalidArgumentException('Table has already joined');
        }
        foreach ($this->joins as $join) {
            if ($alias === $join['alias']) {
                throw new \InvalidArgumentException('Invalid alias name');
            }
        }
        return $hash;
    }

    /**
     * @param array $values
     * @return Select
     */
    public function with(array $values): Select
    {
        foreach ($values as $alias => $value) {
            $alias = $this->escapeIdentifier($alias, false);
            if (!$value instanceof Select) {
                throw new \InvalidArgumentException('Invalid query type');
            }
            if (isset($this->with[$alias])) {
                throw new \InvalidArgumentException('Alias is already in use');
            }
            $this->with[$alias] = $value;
        }
        return $this;
    }

    /**
     * @param array $values
     * @return Select
     */
    public function groupBy(array $values): Select
    {
        $this->groupBy = $values;

        return $this;
    }

    /**
     * @param array $values
     * @return Select
     */
    public function orderBy(array $values): Select
    {
        $this->orderBy = $values;

        return $this;
    }

    /**
     * @param string $condition
     * @return Select
     */
    public function having(string $condition): Select
    {
        $this->having = $condition;

        return $this;
    }

    /**
     * @param int $value
     * @return Select
     */
    public function limit(int $value): Select
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * @param int $value
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
     * @return Select
     */
    public function range(int $offset, int $limit): Select
    {
        $this->offset = $offset;
        $this->limit = $limit;

        return $this;
    }

    public function build(): string
    {
        $this->validateQuery();

        $query = '';
        $query .= $this->buildWith();
        $query .= $this->buildSelect();
        $query .= $this->buildJoins();
        $query .= $this->buildWhere();
        $query .= $this->buildGroupBy();
        $query .= $this->buildHaving();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimitOffset();

        return $query;
    }

    private function validateQuery()
    {
        if (!empty($this->having) && empty($this->groupBy)) {
            throw new \RuntimeException('Using having without group by is unacceptable');
        }

        if ($this->fetchMode === FetchMode::COLUMN && sizeof($this->columns) !== 1) {
            throw new \RuntimeException('Invalid fields number for this fetch mode');
        }
        if ($this->fetchMode === FetchMode::KEY_VALUE && sizeof($this->columns) !== 2) {
            throw new \RuntimeException('Invalid fields number for this fetch mode');
        }
    }

    private function buildWith(): string
    {
        $queries = [];
        foreach ($this->with as $alias => $query) {
            $queries[] = $alias . ' AS (' . $query->build() . ')';
        }

        return !empty($queries) ? 'WITH ' . implode(', ', $queries) : '';
    }

    private function buildSelect(): string
    {
        $str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '');
        $str .= (empty($this->columns) ? '*' : implode(',', $this->columns)) . ' FROM ' . $this->table . ' ' . $this->alias;

        return $str;
    }

    private function buildJoins(): string
    {
        $joins = [];
        foreach ($this->joins as $join) {
            $joins[] = sprintf(
                "%s JOIN %s %s ON %s",
                strtoupper($join['type']),
                $join['table'],
                $join['alias'],
                $join['condition']
            );
        }
        return !empty($joins) ? ' ' . implode(' ', $joins) : '';
    }

    private function buildWhere(): string
    {
        return $this->hasConditions() ? ' WHERE ' . $this->buildConditions() : '';
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