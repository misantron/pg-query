<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Assert\Assert;
use Misantron\QueryBuilder\Expression\Field;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Stringable;

/**
 * Class Query.
 */
abstract class Query implements Stringable
{
    use Escape, Assert;

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
     * @param \PDO        $pdo
     * @param string|null $table
     */
    public function __construct(\PDO $pdo, ?string $table = null)
    {
        $this->pdo = $pdo;

        $table !== null && $this->table = $this->escapeIdentifier($table);
    }

    /**
     * @param string $name
     *
     * @return Query
     */
    public function table(string $name)
    {
        $this->table = $this->escapeIdentifier($name);

        return $this;
    }

    /**
     * @return Query
     */
    public function execute()
    {
        $query = $this->__toString();

        $this->statement = $this->pdo->prepare($query);
        $this->statement->execute();

        return $this;
    }

    /**
     * @param array|string $items
     *
     * @return array
     */
    protected function parseList($items): array
    {
        if (is_string($items)) {
            $items = explode(',', $items);
        }

        return array_filter(array_map(function ($item) {
            return $item instanceof Field
                ? (string)$item
                : $this->escapeIdentifier(trim($item));
        }, $items));
    }
}
