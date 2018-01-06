<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;

class Delete extends Query
{
    use Conditions;

    public function build(): string
    {
        $query = 'DELETE FROM ' . $this->table;

        if (!empty($this->conditions)) {
            $query .= ' WHERE ' . $this->buildConditions();
        }

        return $query;
    }
}