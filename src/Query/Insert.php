<?php

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Assert\Assert;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Query\Mixin\Columns;
use Misantron\QueryBuilder\Query\Mixin\Returning;
use Misantron\QueryBuilder\Query\Mixin\Selectable;
use Misantron\QueryBuilder\Server;

/**
 * Class Insert.
 *
 *
 * @method Insert columns($items)
 * @method Insert returning($items)
 * @method Insert execute()
 */
class Insert extends Query implements Selectable
{
    use Columns, Returning, Assert;

    /**
     * @var array
     */
    private $values;

    /**
     * @var ConflictTarget
     */
    private $conflictTarget;

    /**
     * @var Update
     */
    private $conflictAction;

    /**
     * @var Select
     */
    private $rowSet;

    /**
     * @param Server $server
     * @param string $table
     */
    public function __construct(Server $server, string $table)
    {
        parent::__construct($server);

        $this->table($table);
    }

    /**
     * @param array $items
     *
     * @return Insert
     */
    public function values(array $items): Insert
    {
        $this->assertValuesNotEmpty($items);

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
     * @param ConflictTarget $target
     * @param Update|null    $action
     *
     * @return Insert
     */
    public function onConflict(ConflictTarget $target, ?Update $action = null): Insert
    {
        $this->assertFeatureAvailable('9.5');

        $this->conflictTarget = $target;
        $this->conflictAction = $action;

        return $this;
    }

    /**
     * @param Select $rowSet
     *
     * @return Insert
     */
    public function fromRows(Select $rowSet): Insert
    {
        $this->rowSet = $rowSet;
        $this->columns = $rowSet->getColumns();

        return $this;
    }

    /**
     * @return array
     */
    public function getInsertedRow(): array
    {
        $this->assertReturningSet();
        $this->assertQueryExecuted();

        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getInsertedRows(): array
    {
        $this->assertReturningSet();
        $this->assertQueryExecuted();

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $this->assertColumnsNotEmpty($this->columns);

        $query = sprintf('INSERT INTO %s (%s)', $this->table, implode(',', $this->columns));

        if ($this->rowSet instanceof Select) {
            $query .= ' ' . (string)$this->rowSet;
        } else {
            $query .= $this->buildValues();
            $query .= $this->buildOnConflict();
            $query .= $this->buildReturning();
        }

        return $query;
    }

    /**
     * @return string
     */
    private function buildValues(): string
    {
        $this->assertValuesNotEmpty($this->values);

        $values = [];
        foreach ($this->values as $row) {
            $escaped = array_map(function ($value) {
                return $this->escapeValue($value);
            }, $row);

            $values[] = '(' . implode(',', $escaped) . ')';
        }

        return ' VALUES ' . implode(',', $values);
    }

    /**
     * @return string
     */
    private function buildOnConflict(): string
    {
        $expression = '';
        if ($this->conflictTarget instanceof ConflictTarget) {
            $action = $this->conflictAction instanceof Update ? (string)$this->conflictAction : 'NOTHING';
            $expression .= ' ON CONFLICT ' . (string)$this->conflictTarget . ' DO ' . $action;
        }

        return $expression;
    }
}
