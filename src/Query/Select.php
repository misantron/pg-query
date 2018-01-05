<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Dictionary\FetchMode;

class Select extends Query
{
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

        if ($mode === FetchMode::COLUMN && sizeof($this->columns) !== 1) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
        }
        if ($mode === FetchMode::KEY_VALUE && sizeof($this->columns) !== 2) {
            throw new \InvalidArgumentException('Invalid fields number for this fetch mode');
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
        // validation

        $hash = hash('crc32', $table . '_' . $alias);
        $this->join[$hash] = [
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
     * @return Select
     */
    public function leftJoin(string $table, string $alias, string $condition)
    {
        // validation

        $hash = hash('crc32', $table . '_' . $alias);
        $this->join[$hash] = [
            'type' => 'left',
            'table' => pg_escape_identifier($table),
            'alias' => $alias,
            'condition' => $condition,
        ];
        return $this;
    }

    public function build(): string
    {
        return '';
    }
}