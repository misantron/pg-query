<?php

namespace MediaTech;


use MediaTech\Query\FetchMode;
use MediaTech\Query\Type;

class Criteria
{
    const DEFAULT_TABLE_ALIAS = 't1';

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $fetchMode;

    protected $queryParts = [
        'fields' => [],
        'table' => [],
        'join' => [],
        'where' => [],
        'having' => [],
        'orderBy' => [],
        'groupBy' => [],
        'with' => [],
        'set' => [],
        'values' => [],
        'limit' => null,
        'offset' => null,
    ];

    /**
     * @param \PDO $connection
     */
    private function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param \PDO $connection
     * @return Criteria
     */
    public static function create(\PDO $connection)
    {
        return new static($connection);
    }

    /**
     * @param \PDO $connection
     * @return Criteria
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @param int $mode
     * @return Criteria
     */
    public function setFetchMode($mode)
    {
        if (!in_array($mode, FetchMode::getKeys())) {
            throw new \InvalidArgumentException('Invalid fetch mode');
        }

        if ($mode === FetchMode::COLUMN && sizeof($this->queryParts['fields']) !== 1) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
        }
        if ($mode === FetchMode::KEY_VALUE && sizeof($this->queryParts['fields']) !== 2) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
        }

        $this->fetchMode = $mode;

        return $this;
    }

    /**
     * @param Criteria|string $query
     * @param string $alias
     * @return Criteria
     */
    public function addWith($query, string $alias)
    {
        if (isset($this->queryParts['with'][$alias])) {
            throw new \InvalidArgumentException('Alias name has already in use');
        }

        $this->queryParts['with'][$alias] = (string)$query;

        return $this;
    }

    /**
     * @param Criteria[]|string[] $queries
     * @return Criteria
     */
    public function with(array $queries)
    {
        foreach ($queries as $alias => $query) {
            $this->addWith($query, $alias);
        }
        return $this;
    }

    /**
     * @param string|array $fields
     * @return Criteria
     */
    public function fields($fields)
    {
        $this->queryParts['fields'] = $this->parseFields($fields);

        return $this;
    }

    /**
     * @param string $field
     * @return Criteria
     */
    public function addField($field)
    {
        if ($this->type !== Type::SELECT || $this->type !== Type::INSERT) {
            throw new \InvalidArgumentException(
                sprintf('Unable to add field to %s query', $this->type)
            );
        }

        $this->queryParts['fields'][] = (string)$field;

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function select(string $table, $alias = null)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is empty');
        }

        $this->type = Type::SELECT;
        $this->fetchMode = FetchMode::ASSOC;

        $this->queryParts['table'] = [
            'name' => pg_escape_identifier($table),
            'alias' => $alias ?: self::DEFAULT_TABLE_ALIAS,
        ];
        return $this;
    }

    /**
     * @param string $alias
     * @return Criteria
     */
    public function setAlias(string $alias)
    {
        if (empty($alias)) {
            throw new \InvalidArgumentException('Alias name is empty');
        }

        $this->queryParts['table']['alias'] = $alias;

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function update(string $table, $alias = null)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is empty');
        }

        $this->type = Type::UPDATE;

        $this->queryParts['table'] = [
            'name' => pg_escape_identifier($table),
            'alias' => $alias ?: self::DEFAULT_TABLE_ALIAS,
        ];

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function delete(string $table, $alias = null)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is empty');
        }

        $this->type = Type::DELETE;

        $this->queryParts['table'] = [
            'name' => pg_escape_identifier($table),
            'alias' => $alias ?: self::DEFAULT_TABLE_ALIAS,
        ];

        return $this;
    }

    /**
     * @param string $table
     * @return Criteria
     */
    public function insert(string $table)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->type = Type::INSERT;

        $this->queryParts['table']['name'] = pg_escape_identifier($table);

        return $this;
    }

    /**
     * @param array $data
     * @return Criteria
     */
    public function set(array $data)
    {
        if ($this->type !== Type::UPDATE) {
            throw new \BadMethodCallException('Invalid query type');
        }

        foreach ($data as $field => $value) {
            $this->queryParts['set'][pg_escape_identifier($field)] = $value;
        }
        return $this;
    }

    /**
     * @param array $items
     * @return Criteria
     */
    public function values(array $items)
    {
        if ($this->type !== Type::INSERT) {
            throw new \BadMethodCallException('Invalid query type');
        }

        if ($items === array_values($items)) {
            // extract field names from data rows
            if (sizeof($this->queryParts['fields']) === 0) {
                $this->queryParts['fields'] = array_keys($items[0]);
            }
            $this->queryParts['values'] = array_map('array_values', $items);
        } else {
            $this->queryParts['fields'] = array_keys($items);
            $this->queryParts['values'][] = array_values($items);
        }

        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Criteria
     */
    public function join($table, $alias, $condition)
    {
        return $this->innerJoin($table, $alias, $condition);
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Criteria
     */
    public function innerJoin(string $table, string $alias, string $condition)
    {
        $this->validateJoin($alias);
        $hash = $this->tableHash($table, $alias);
        $this->queryParts['join'][$hash] = [
            'type' => 'inner',
            'table' => pg_escape_identifier($table),
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Criteria
     */
    public function leftJoin(string $table, string $alias, string $condition)
    {
        $this->validateJoin($alias);
        $hash = $this->tableHash($table, $alias);
        $this->queryParts['join'][$hash] = [
            'type' => 'left',
            'table' => pg_escape_identifier($table),
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @return Criteria
     */
    public function beginGroup()
    {
        $this->queryParts['where'][] = '(';

        return $this;
    }

    /**
     * @return Criteria
     */
    public function andBeginGroup()
    {
        if (empty($this->queryParts['where'])) {
            $this->beginGroup();
        } else {
            $this->queryParts['where'][] = 'AND (';
        }

        return $this;
    }

    /**
     * @return Criteria
     */
    public function orBeginGroup()
    {
        $this->queryParts['where'][] = 'OR (';

        return $this;
    }

    /**
     * @return Criteria
     */
    public function endGroup()
    {
        $this->queryParts['where'][] = ')';

        return $this;
    }

    public function limit(int $value)
    {
        $this->queryParts['limit'] = $value;

        return $this;
    }

    public function offset(int $value)
    {
        $this->queryParts['offset'] = $value;

        return $this;
    }

    private function build()
    {
        return '';
    }

    public function __toString()
    {
        return $this->build();
    }

    /**
     * @param string $alias
     */
    private function validateJoin($alias)
    {
        $mainTableAlias = isset($this->queryParts['from']['alias']) ?
            $this->queryParts['from']['alias'] : self::DEFAULT_TABLE_ALIAS;
        if ($mainTableAlias === $alias) {
            throw new \InvalidArgumentException('Invalid alias name');
        }
        foreach ($this->queryParts['join'] as $join) {
            if ($alias === $join['alias']) {
                throw new \InvalidArgumentException('Invalid alias name');
            }
        }
    }

    /**
     * @param string $table
     * @param string $alias
     * @return string
     */
    private function tableHash(string &$table, string &$alias)
    {
        return hash('crc32', $table . '_' . $alias);
    }

    /**
     * @param array|string $fields
     * @return array
     */
    private function parseFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        return array_filter(array_map(function (string $field) {
            return pg_escape_identifier(trim($field));
        }, $fields));
    }
}