<?php

namespace MediaTech;


use MediaTech\Query\FetchMode;
use MediaTech\Query\Type;

class Criteria
{
    const DEFAULT_ALIAS = 't1';

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
            throw new \InvalidArgumentException('Invalid table name');
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

        $this->queryParts['table'] = [
            'name' => $table,
        ];

        return $this;
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