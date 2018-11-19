<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Returning;

/**
 * Class Delete.
 */
class Delete extends Query implements Filterable
{
    use Filters, Returning;

    /**
     * @param \PDO   $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        parent::__construct($pdo, $table);

        $this->filters = new FilterGroup();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $query = 'DELETE FROM ' . $this->table;
        $query .= $this->buildFilters();
        $query .= $this->buildReturning();

        return $query;
    }
}
