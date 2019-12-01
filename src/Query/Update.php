<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Returning;
use Misantron\QueryBuilder\Server;

/**
 * Class Update.
 *
 * @method Update table(string $name)
 * @method Update execute()
 * @method Update returning($items)
 * @method Update beginGroup()
 * @method Update andGroup()
 * @method Update orGroup()
 * @method Update endGroup()
 * @method Update equals(string $column, $value)
 * @method Update andEquals(string $column, $value)
 * @method Update orEquals(string $column, $value)
 * @method Update notEquals(string $column, $value)
 * @method Update andNotEquals(string $column, $value)
 * @method Update orNotEquals(string $column, $value)
 * @method Update more(string $column, $value)
 * @method Update andMore(string $column, $value)
 * @method Update orMore(string $column, $value)
 * @method Update moreOrEquals(string $column, $value)
 * @method Update andMoreOrEquals(string $column, $value)
 * @method Update orMoreOrEquals(string $column, $value)
 * @method Update less(string $column, $value)
 * @method Update andLess(string $column, $value)
 * @method Update orLess(string $column, $value)
 * @method Update lessOrEquals(string $column, $value)
 * @method Update andLessOrEquals(string $column, $value)
 * @method Update orLessOrEquals(string $column, $value)
 * @method Update between(string $column, array $values)
 * @method Update andBetween(string $column, array $values)
 * @method Update orBetween(string $column, array $values)
 * @method Update in(string $column, array $values)
 * @method Update andIn(string $column, array $values)
 * @method Update orIn(string $column, array $values)
 * @method Update notIn(string $column, array $values)
 * @method Update andNotIn(string $column, array $values)
 * @method Update orNotIn(string $column, array $values)
 * @method Update inArray(string $column, $value)
 * @method Update andInArray(string $column, $value)
 * @method Update orInArray(string $column, $value)
 * @method Update notInArray(string $column, $value)
 * @method Update andNotInArray(string $column, $value)
 * @method Update orNotInArray(string $column, $value)
 * @method Update arrayContains(string $column, array $values)
 * @method Update andArrayContains(string $column, array $values)
 * @method Update orArrayContains(string $column, array $values)
 * @method Update isNull(string $column)
 * @method Update andIsNull(string $column)
 * @method Update orIsNull(string $column)
 * @method Update isNotNull(string $column)
 * @method Update andIsNotNull(string $column)
 * @method Update orIsNotNull(string $column)
 */
final class Update extends Query implements Filterable
{
    use Filters;
    use Returning;

    /**
     * @var array
     */
    private $set = [];

    public function __construct(Server $server)
    {
        parent::__construct($server);

        $this->filters = new FilterGroup();
    }

    public function set(array $data): Update
    {
        QueryAssert::valuesNotEmpty($data);

        foreach ($data as $field => $value) {
            $this->set[$this->escapeIdentifier($field)] = $this->escapeValue($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        $query = 'UPDATE ' . ($this->table ? $this->table . ' ' : '');
        $query .= 'SET ' . $this->buildSet();
        $query .= $this->buildFilters();
        $query .= $this->buildReturning();

        return $query;
    }

    private function buildSet(): string
    {
        $set = $this->set;

        QueryAssert::querySetPartNotEmpty($set);

        $values = array_map(static function (string $field, string $value) {
            return $field . ' = ' . $value;
        }, array_keys($set), array_values($set));

        return implode(',', $values);
    }
}
