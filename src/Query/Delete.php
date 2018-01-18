<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;

/**
 * Class Delete
 * @package MediaTech\Query\Query
 */
class Delete extends Query
{
    use Conditions;

    /**
     * @return string
     */
    public function build(): string
    {
        $query = 'DELETE FROM ' . $this->table;
        $query .= $this->buildConditions();

        return $query;
    }
}