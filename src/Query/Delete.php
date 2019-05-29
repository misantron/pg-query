<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Returning;
use Misantron\QueryBuilder\Server;

/**
 * Class Delete.
 *
 *
 * @method Delete table(string $name)
 * @method Delete execute()
 */
class Delete extends Query implements Filterable
{
    use Filters, Returning;

    /**
     * @param Server $server
     * @param string $table
     */
    public function __construct(Server $server, string $table)
    {
        parent::__construct($server);

        $this->table($table);
        $this->filters = new FilterGroup();
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        $query = 'DELETE FROM ' . $this->table;
        $query .= $this->buildFilters();
        $query .= $this->buildReturning();

        return $query;
    }
}
