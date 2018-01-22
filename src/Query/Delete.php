<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Filter\FilterGroup;
use MediaTech\Query\Query\Mixin\Filterable;
use MediaTech\Query\Query\Mixin\Filters;

/**
 * Class Delete
 * @package MediaTech\Query\Query
 */
class Delete extends Query implements Filterable
{
    use Filters;

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

        return $query;
    }
}