<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;
use MediaTech\Query\Query\Mixin\Filterable;

/**
 * Class Select
 * @package MediaTech\Query\Query
 */
class Select extends Query implements Filterable
{
    use Conditions;

    const DEFAULT_TABLE_ALIAS = 't1';

    const AVAILABLE_FETCH_MODES = [
        \PDO::FETCH_ASSOC,
        \PDO::FETCH_CLASS,
        \PDO::FETCH_KEY_PAIR,
        \PDO::FETCH_COLUMN,
        \PDO::FETCH_FUNC,
    ];

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
        int $fetchMode = \PDO::FETCH_ASSOC
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
     * @param array|string $items
     * @return Select
     */
    public function columns($items): Select
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }

        $this->columns = $this->parseList($items);

        return $this;
    }

    /**
     * @param bool $value
     * @return Select
     */
    public function distinct(bool $value = true): Select
    {
        $this->distinct = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return Select
     */
    public function fetchMode(int $value): Select
    {
        if (!in_array($value, self::AVAILABLE_FETCH_MODES)) {
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
        $table = $this->escapeIdentifier($table, false);
        $alias = $this->escapeIdentifier($alias, false);

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
        $query .= $this->buildConditions();
        $query .= $this->buildGroupBy();
        $query .= $this->buildHaving();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimitOffset();

        return $query;
    }

    /**
     * @param string|callable $argument
     * @return array
     */
    public function fetchAll($argument = null): array
    {
        switch ($this->fetchMode) {
            case \PDO::FETCH_CLASS:
            case \PDO::FETCH_FUNC:
                $data = $this->statement->fetchAll($this->fetchMode, $argument);
                break;
            case \PDO::FETCH_ASSOC:
            case \PDO::FETCH_KEY_PAIR:
            case \PDO::FETCH_COLUMN:
                $data = $this->statement->fetchAll($this->fetchMode);
                break;
            default:
                throw new \RuntimeException('Invalid fetch mode');
        }
        return $data;
    }

    /**
     * @param string $argument
     * @return mixed
     */
    public function fetchOne($argument = null)
    {
        switch ($this->fetchMode) {
            case \PDO::FETCH_CLASS:
                $data = $this->statement->fetchObject($argument);
                break;
            case \PDO::FETCH_ASSOC:
                $data = $this->statement->fetch();
                break;
            case \PDO::FETCH_COLUMN:
                $data = $this->statement->fetchColumn();
                break;
            default:
                throw new \RuntimeException('Invalid fetch mode');
        }
        return $data;
    }

    private function validateQuery()
    {
        if (!empty($this->having) && empty($this->groupBy)) {
            throw new \RuntimeException('Using having without group by is unacceptable');
        }

        if ($this->fetchMode === \PDO::FETCH_COLUMN && sizeof($this->columns) !== 1) {
            throw new \RuntimeException('Invalid fields number for this fetch mode');
        }
        if ($this->fetchMode === \PDO::FETCH_KEY_PAIR && sizeof($this->columns) !== 2) {
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