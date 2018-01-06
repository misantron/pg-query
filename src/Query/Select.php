<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Dictionary\FetchMode;
use MediaTech\Query\Query\Mixin\Conditions;

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
    private $columns;

    /**
     * @var array
     */
    private $join;

    private $groupBy;

    private $orderBy;

    private $having;

    private $limit;

    private $offset;

    /**
     * @var int
     */
    private $fetchMode;

    /**
     * @var Select[]
     */
    private $with;

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

    public function alias(string $value)
    {
        $this->alias = $this->escapeIdentifier($value, false);

        return $this;
    }

    public function columns(array $items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException();
        }

        $this->columns = $this->parseColumns($items);

        return $this;
    }

    public function setFetchMode(int $mode)
    {
        if (!in_array($mode, FetchMode::getKeys())) {
            throw new \InvalidArgumentException('Invalid fetch mode');
        }

        $this->fetchMode = $mode;

        return $this;
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Select
     */
    public function join(string $table, string $alias, string $condition)
    {
        return $this->innerJoin($table, $alias, $condition);
    }

    /**
     * @param string $table
     * @param string $alias
     * @param string $condition
     * @return Select
     */
    public function innerJoin(string $table, string $alias, string $condition)
    {
        $table = $this->escapeIdentifier($table, false);
        $alias = $this->escapeIdentifier($alias, false);

        $hash = $this->validateJoin($table, $alias);

        $this->join[$hash] = [
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
    public function leftJoin(string $table, string $alias, string $condition)
    {
        $table = $this->escapeIdentifier($table, false);
        $alias = $this->escapeIdentifier($alias, false);

        $hash = $this->validateJoin($table, $alias);

        $this->join[$hash] = [
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

        if (isset($this->join[$hash])) {
            throw new \InvalidArgumentException('Table has already joined');
        }
        foreach ($this->join as $join) {
            if ($alias === $join['alias']) {
                throw new \InvalidArgumentException('Invalid alias name');
            }
        }
        return $hash;
    }

    public function limit(int $value)
    {
        $this->limit = $value;

        return $this;
    }

    public function offset(int $value)
    {
        $this->offset = $value;

        return $this;
    }

    public function build(): string
    {
        if ($this->fetchMode === FetchMode::COLUMN && sizeof($this->columns) !== 1) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
        }
        if ($this->fetchMode === FetchMode::KEY_VALUE && sizeof($this->columns) !== 2) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
        }

        return '';
    }
}