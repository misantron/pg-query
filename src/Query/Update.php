<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Returning;

/**
 * Class Update.
 *
 *
 * @method Update table(string $name)
 */
class Update extends Query implements Filterable
{
    use Filters, Returning;

    /**
     * @var array
     */
    private $set = [];

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);

        $this->filters = new FilterGroup();
    }

    /**
     * @param array $data
     *
     * @return Update
     */
    public function set(array $data): Update
    {
        $this->assertValuesNotEmpty($data);

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
        $query = 'UPDATE ' . ($this->table ? $this->table . ' ' : '');
        $query .= 'SET ' . $this->buildSet();
        $query .= $this->buildFilters();
        $query .= $this->buildReturning();

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
