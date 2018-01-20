<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Filter\FilterGroup;
use MediaTech\Query\Query\Mixin\Filters;
use MediaTech\Query\Query\Mixin\Filterable;

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
     * @return string
     */
    public function build(): string
    {
        $query = 'DELETE FROM ' . $this->table;
        $query .= $this->filters->notEmpty() ? ' WHERE ' . $this->filters->build() : '';

        return $query;
    }
}