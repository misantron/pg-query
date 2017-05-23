<?php

namespace MediaTech;


use MediaTech\Query\FetchMode;
use MediaTech\Query\Type;

class Criteria
{
    const DEFAULT_ALIAS = 't';

    /**
     * @var \PDO
     */
    private $connection;

    protected $queryParts = [
        'fields' => [],
        'table' => [],
        'join' => [],
        'where' => [],
        'having' => [],
        'orderBy' => [],
        'groupBy' => [],
        'params' => [],
        'with' => [],
        'set' => [],
        'values' => [],
    ];

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var
     */
    protected $limit;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $fetchMode;

    private function __construct(\PDO $connection)
    {
        $this->connection = $connection;

        $this->type = Type::SELECT;
        $this->fetchMode = FetchMode::ASSOC;
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
     * @param string|array $fields
     * @return Criteria
     */
    public function fields($fields)
    {
        $this->queryParts['fields'] = $this->prepareFields($fields);

        return $this;
    }

    /**
     * @param string $field
     * @return Criteria
     */
    public function addField($field)
    {
        if ($this->type !== Type::SELECT) {
            throw new \InvalidArgumentException('Unable to add field to non-select query');
        }

        $this->queryParts['fields'][] = (string)$field;

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function select($table, $alias = null)
    {
        if (!is_string($table) || empty($table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->queryParts['table'] = [
            'name' => $table,
            'alias' => $alias ?: self::DEFAULT_ALIAS,
        ];
        return $this;
    }

    /**
     * @param string $alias
     * @return Criteria
     */
    public function setAlias($alias)
    {
        if (!is_string($alias) || empty($alias)) {
            throw new \InvalidArgumentException('Invalid alias name');
        }

        $this->queryParts['table']['alias'] = $alias;

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function update($table, $alias = null)
    {
        if (!is_string($table) || empty($table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->type = Type::UPDATE;

        $this->queryParts['table'] = [
            'name' => $table,
            'alias' => $alias ?: self::DEFAULT_ALIAS,
        ];

        return $this;
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @return Criteria
     */
    public function delete($table, $alias = null)
    {
        if (!is_string($table) || empty($table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->type = Type::DELETE;

        $this->queryParts['table'] = [
            'name' => $table,
            'alias' => $alias ?: self::DEFAULT_ALIAS,
        ];

        return $this;
    }

    /**
     * @param string $table
     * @return Criteria
     */
    public function insert($table)
    {
        if (!is_string($table) || empty($table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->type = Type::INSERT;

        $this->queryParts['table']['name'] = $table;

        return $this;
    }

    /**
     * @param array $data
     * @return Criteria
     */
    public function set(array $data)
    {
        if ($this->type !== Type::UPDATE) {
            throw new \BadMethodCallException('Invalid method call');
        }
        foreach ($data as $field => $value) {
            $this->queryParts['set'][$field] = $value;
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
            throw new \BadMethodCallException('Invalid method call');
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
    public function innerJoin($table, $alias, $condition)
    {
        $this->validateJoin($alias);
        $hash = $this->tableHash($table, $alias);
        $this->queryParts['join'][$hash] = [
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
     * @return Criteria
     */
    public function leftJoin($table, $alias, $condition)
    {
        $this->validateJoin($alias);
        $hash = $this->tableHash($table, $alias);
        $this->queryParts['join'][$hash] = [
            'type' => 'left',
            'table' => $table,
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    /**
     * @param string $alias
     */
    private function validateJoin($alias)
    {
        $mainTableAlias = isset($this->queryParts['from']['alias']) ? $this->queryParts['from']['alias'] : self::DEFAULT_ALIAS;
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
    private function tableHash(&$table, &$alias)
    {
        return hash('crc32', $table . '_' . $alias);
    }

    /**
     * @param array|string $fields
     * @return array
     */
    private function prepareFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }
        $res = array_map('trim', $fields);

        return $res;
    }
}