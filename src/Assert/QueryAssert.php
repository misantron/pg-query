<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Assert;

use Misantron\QueryBuilder\Exception\QueryLogicException;
use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Query;
use Misantron\QueryBuilder\Query\Select;
use PDOStatement;

/**
 * Class QueryAssert.
 */
final class QueryAssert
{
    /**
     * @param array $items
     *
     * @throws QueryParameterException
     */
    public static function valuesNotEmpty($items): void
    {
        if (empty($items)) {
            throw QueryParameterException::emptyValue('Value list');
        }
    }

    /**
     * @param array|string $items
     *
     * @throws QueryParameterException
     */
    public static function columnsNotEmpty($items): void
    {
        if (empty($items)) {
            throw QueryParameterException::emptyValue('Column list');
        }
    }

    /**
     * @throws QueryParameterException
     */
    public static function filterGroupNotEmpty(FilterGroup $group): void
    {
        if ($group->isEmpty()) {
            throw QueryParameterException::emptyValue('Condition');
        }
    }

    /**
     * @param mixed $value
     *
     * @throws QueryParameterException
     */
    public static function valueIsScalar($value): void
    {
        if (!is_scalar($value)) {
            throw QueryParameterException::notTypeOf('scalar');
        }
    }

    /**
     * @throws QueryParameterException
     */
    public static function valueIsSelectQuery(Query $query): void
    {
        if (!$query instanceof Select) {
            throw QueryParameterException::notTypeOf('select query instance');
        }
    }

    /**
     * @throws QueryParameterException
     */
    public static function numberOfElements(array $values, int $number): void
    {
        if (count($values) !== $number) {
            throw QueryParameterException::numberOfElements($number);
        }
    }

    /**
     * @throws QueryParameterException
     */
    public static function validConditionOperator(string $operator, array $operators): void
    {
        if (!empty($operators) && !in_array($operator, $operators, true)) {
            throw QueryParameterException::unexpectedValue('condition', $operator);
        }
    }

    /**
     * @throws QueryParameterException
     */
    public static function validConjunctionOperator(string $operator): void
    {
        if (!in_array(strtolower($operator), ['and', 'or', ''], true)) {
            throw QueryParameterException::unexpectedValue('conjunction', $operator);
        }
    }

    /**
     * @throws QueryLogicException
     */
    public static function tableJoinPossible(array $joins, string $alias, string $hash): void
    {
        if (isset($joins[$hash])) {
            throw QueryLogicException::tableAlreadyJoined();
        }

        foreach ($joins as $join) {
            if ($alias === $join['alias']) {
                throw QueryLogicException::aliasAlreadyInUse();
            }
        }
    }

    /**
     * @throws QueryRuntimeException
     */
    public static function returningConditionSet(array $returning): void
    {
        if (empty($returning)) {
            throw QueryRuntimeException::emptyReturningQueryPart();
        }
    }

    /**
     * @throws QueryRuntimeException
     */
    public static function querySetPartNotEmpty(array $set): void
    {
        if (empty($set)) {
            throw QueryRuntimeException::emptySetQueryPart();
        }
    }

    /**
     * @param PDOStatement|bool $statement
     *
     * @throws QueryRuntimeException
     */
    public static function queryExecuted($statement): void
    {
        if (!$statement instanceof PDOStatement) {
            throw QueryRuntimeException::fetchBeforeExecute();
        }
    }
}
