<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Escape;
use MediaTech\Query\Expression\Field;
use MediaTech\Query\Renderable;

/**
 * Class Query
 * @package MediaTech\Query\Query
 */
abstract class Query implements Renderable
{
    use Escape;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is empty');
        }

        $this->pdo = $pdo;
        $this->table = $this->escapeIdentifier($table, false);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @return Query
     */
    public function execute()
    {
        $query = $this->build();

        $this->statement = $this->pdo->prepare($query);
        $this->statement->execute();

        return $this;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function escapeValue($value): string
    {
        if (is_numeric($value)) {
            $escaped = $value;
        } elseif (is_null($value)) {
            $escaped = 'null';
        } elseif (is_bool($value)) {
            $escaped = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $escaped = $this->escapeArray($value);
        } else {
            $escaped = $this->pdo->quote($value, \PDO::PARAM_STR);
        }
        return $escaped;
    }

    /**
     * @param array|string $items
     * @return array
     */
    protected function parseList($items): array
    {
        if (is_string($items)) {
            $items = explode(',', $items);
        }

        return array_filter(array_map(function ($item) {
            return $item instanceof Field ?
                $item->build() :
                $this->escapeIdentifier(trim($item), false);
        }, $items));
    }
}