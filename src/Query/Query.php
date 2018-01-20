<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Expression\Field;
use MediaTech\Query\Helper\Escape;
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