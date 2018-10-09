<?php

namespace Misantron\QueryBuilder\Query;


use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;

/**
 * Class Update
 * @package Misantron\QueryBuilder\Query
 */
class Update extends Query implements Filterable
{
    use Filters;

    /**
     * @var array
     */
    private $set = [];

    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        parent::__construct($pdo, $table);

        $this->filters = new FilterGroup();
    }

    /**
     * @param array $data
     * @return Update
     */
    public function set(array $data): Update
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Value list is empty');
        }

        foreach ($data as $field => $value) {
            $this->set[$this->escapeIdentifier($field)] = $this->escapeValue($value);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $query = 'UPDATE ' . $this->table . ' SET ' . $this->buildSet();
        $query .= $this->buildFilters();

        return $query;
    }

    /**
     * @return string
     */
    private function buildSet(): string
    {
        $set = $this->set;

        if (empty($set)) {
            throw new \RuntimeException('Query set is empty');
        }

        $values = array_map(function (string $field, string $value) {
            return $field . ' = ' . $value;
        }, array_keys($set), array_values($set));

        return implode(',', $values);
    }
}