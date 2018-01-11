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

    /**
     * @var array
     */
    private $groupBy;

    /**
     * @var array
     */
    private $orderBy;

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
     * @param int $mode
     * @return Select
     */
    public function setFetchMode(int $mode): Select
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
    public function leftJoin(string $table, string $alias, string $condition): Select
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

    /**
     * @param array $values
     * @return Select
     */
    public function with(array $values): Select
    {
        foreach ($values as $alias => $value) {
            $alias = $this->escapeIdentifier($alias, false);
            if (!$value instanceof Select) {
                throw new \InvalidArgumentException('');
            }
            if (isset($this->with[$alias])) {
                throw new \InvalidArgumentException('');
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

    public function build(): string
    {
        $this->validateQuery();

        return '';
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
}