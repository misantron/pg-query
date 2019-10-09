<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Mixin\Filterable;
use Misantron\QueryBuilder\Query\Mixin\Filters;
use Misantron\QueryBuilder\Query\Mixin\Returning;
use Misantron\QueryBuilder\Server;

/**
 * Class Delete.
 *
 *
 * @method Delete table(string $name)
 * @method Delete execute()
 * @method Delete returning($items)
 * @method Delete beginGroup()
 * @method Delete andGroup()
 * @method Delete orGroup()
 * @method Delete endGroup()
 * @method Delete equals(string $column, $value)
 * @method Delete andEquals(string $column, $value)
 * @method Delete orEquals(string $column, $value)
 * @method Delete notEquals(string $column, $value)
 * @method Delete andNotEquals(string $column, $value)
 * @method Delete orNotEquals(string $column, $value)
 * @method Delete more(string $column, $value)
 * @method Delete andMore(string $column, $value)
 * @method Delete orMore(string $column, $value)
 * @method Delete moreOrEquals(string $column, $value)
 * @method Delete andMoreOrEquals(string $column, $value)
 * @method Delete orMoreOrEquals(string $column, $value)
 * @method Delete less(string $column, $value)
 * @method Delete andLess(string $column, $value)
 * @method Delete orLess(string $column, $value)
 * @method Delete lessOrEquals(string $column, $value)
 * @method Delete andLessOrEquals(string $column, $value)
 * @method Delete orLessOrEquals(string $column, $value)
 * @method Delete between(string $column, array $values)
 * @method Delete andBetween(string $column, array $values)
 * @method Delete orBetween(string $column, array $values)
 * @method Delete in(string $column, array $values)
 * @method Delete andIn(string $column, array $values)
 * @method Delete orIn(string $column, array $values)
 * @method Delete notIn(string $column, array $values)
 * @method Delete andNotIn(string $column, array $values)
 * @method Delete orNotIn(string $column, array $values)
 * @method Delete inArray(string $column, $value)
 * @method Delete andInArray(string $column, $value)
 * @method Delete orInArray(string $column, $value)
 * @method Delete notInArray(string $column, $value)
 * @method Delete andNotInArray(string $column, $value)
 * @method Delete orNotInArray(string $column, $value)
 * @method Delete arrayContains(string $column, array $values)
 * @method Delete andArrayContains(string $column, array $values)
 * @method Delete orArrayContains(string $column, array $values)
 * @method Delete isNull(string $column)
 * @method Delete andIsNull(string $column)
 * @method Delete orIsNull(string $column)
 * @method Delete isNotNull(string $column)
 * @method Delete andIsNotNull(string $column)
 * @method Delete orIsNotNull(string $column)
 */
final class Delete extends Query implements Filterable
{
    use Filters;
    use Returning;

    /**
     * @param Server $server
     * @param string $table
     */
    public function __construct(Server $server, string $table)
    {
        parent::__construct($server);

        $this->table($table);
        $this->filters = new FilterGroup();
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        $query = 'DELETE FROM ' . $this->table;
        $query .= $this->buildFilters();
        $query .= $this->buildReturning();

        return $query;
    }
}
