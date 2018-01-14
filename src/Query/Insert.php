<?php

namespace MediaTech\Query\Query;


class Insert extends Query
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $values;

    /**
     * @var Select
     */
    private $rowSet;

    /**
     * @param array|string $items
     * @return Insert
     */
    public function columns($items): Insert
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Column list is empty');
        }

        $this->columns = $this->parseList($items);

        return $this;
    }

    /**
     * @param array $items
     * @return Insert
     */
    public function values(array $items): Insert
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Value list is empty');
        }

        if ($items === array_values($items)) {
            // extract column names from the first element of data rows
            if (empty($this->columns)) {
                $this->columns = array_keys($items[0]);
            }
            $this->values = array_map('array_values', $items);
        } else {
            $this->columns = array_keys($items);
            $this->values[] = array_values($items);
        }

        return $this;
    }

    /**
     * @param Select $rowSet
     * @return Insert
     */
    public function fromRows(Select $rowSet): Insert
    {
        $this->rowSet = $rowSet;

        return $this;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $query = sprintf('INSERT INTO %s (%s)', $this->table, implode(',', $this->columns));

        if ($this->rowSet instanceof Select) {
            $query .= ' ' . $this->rowSet->build();
        } else {
            $values = [];
            foreach ($this->values as $k => $row) {
                foreach ($row as $i => $value) {
                    $values[$k][$i] = $this->escapeValue($value);
                }
            }

            $values = array_map(function (array $row) {
                return '(' . implode(',', $row) . ')';
            }, $values);

            $query .= ' VALUES ' . implode(',', $values) . ' RETURNING *';
        }

        return $query;
    }
}