<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Compilable;
use Misantron\QueryBuilder\Expression\Field;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Server;
use PDOStatement;

/**
 * Class Query.
 */
abstract class Query implements Compilable
{
    use Escape;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var PDOStatement
     */
    protected $statement;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param string $name
     *
     * @return Query
     */
    public function table(string $name): Query
    {
        $this->table = $this->escapeIdentifier($name);

        return $this;
    }

    /**
     * @return Query
     */
    public function execute(): Query
    {
        $query = $this->compile();

        $this->statement = $this->server->pdo()->prepare($query);
        if ($this->statement instanceof PDOStatement) {
            $this->statement->execute();
        }

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
                ? $item->compile()
                : $this->escapeIdentifier(trim($item));
        }, $items));
    }
}
