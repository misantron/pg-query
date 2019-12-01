<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Assert\ServerAssert;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Expression\OnConflict;
use Misantron\QueryBuilder\Query\Mixin\Columns;
use Misantron\QueryBuilder\Query\Mixin\Returning;
use Misantron\QueryBuilder\Query\Mixin\Selectable;
use Misantron\QueryBuilder\Server;
use PDO;

/**
 * Class Insert.
 *
 * @method Insert table(string $name)
 * @method Insert columns($items)
 * @method Insert execute()
 * @method Insert returning($items)
 */
final class Insert extends Query implements Selectable
{
    use Columns;
    use Returning;

    /**
     * @var array
     */
    private $values;

    /**
     * @var OnConflict|null
     */
    private $onConflict;

    /**
     * @var Select|null
     */
    private $rowSet;

    public function __construct(Server $server, string $table)
    {
        parent::__construct($server);

        $this->table($table);
    }

    /**
     * @return Insert
     */
    public function values(array $items): Insert
    {
        QueryAssert::valuesNotEmpty($items);

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
     * @return Insert
     */
    public function onConflict(ConflictTarget $target, ?Update $action = null): Insert
    {
        ServerAssert::engineFeatureAvailable($this->server, '9.5');

        $this->onConflict = new OnConflict($target, $action);

        return $this;
    }

    /**
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
        QueryAssert::returningConditionSet($this->returning);
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getInsertedRows(): array
    {
        QueryAssert::returningConditionSet($this->returning);
        QueryAssert::queryExecuted($this->statement);

        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        QueryAssert::columnsNotEmpty($this->columns);

        $query = sprintf('INSERT INTO %s (%s)', $this->table, implode(',', $this->columns));

        if ($this->rowSet instanceof Select) {
            $query .= ' ' . $this->rowSet->compile();
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
        QueryAssert::valuesNotEmpty($this->values);

        $values = '';
        foreach ($this->values as $row) {
            $escaped = array_map(function ($value) {
                return $this->escapeValue($value);
            }, $row);

            $values .= '(' . implode(',', $escaped) . '),';
        }

        return ' VALUES ' . rtrim($values, ',');
    }

    /**
     * @return string
     */
    private function buildOnConflict(): string
    {
        return $this->onConflict instanceof OnConflict ? $this->onConflict->compile() : '';
    }
}
