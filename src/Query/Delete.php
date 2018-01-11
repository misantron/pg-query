<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;

class Delete extends Query
{
    use Conditions;

    /**
     * @return string
     */
    public function build(): string
    {
        $query = 'DELETE FROM ' . $this->table;

        if ($this->hasConditions()) {
            $query .= ' WHERE ' . $this->buildConditions();
        }

        return $query;
    }
}