<?php

namespace Misantron\QueryBuilder\Assert;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Exception\ServerException;
use Misantron\QueryBuilder\Exception\QueryLogicException;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Query;
use Misantron\QueryBuilder\Query\Select;
use Misantron\QueryBuilder\Server;

/**
 * Class Assert.
 */
class Assert
{
    private const CONNECTION_OPTIONS = [
        \PDO::ATTR_AUTOCOMMIT,
        \PDO::ATTR_TIMEOUT,
        \PDO::ATTR_ERRMODE,
        \PDO::ATTR_CASE,
        \PDO::ATTR_CURSOR_NAME,
        \PDO::ATTR_CURSOR,
        \PDO::ATTR_PERSISTENT,
        \PDO::ATTR_STATEMENT_CLASS,
        \PDO::ATTR_FETCH_TABLE_NAMES,
        \PDO::ATTR_FETCH_CATALOG_NAMES,
        \PDO::ATTR_STRINGIFY_FETCHES,
        \PDO::ATTR_MAX_COLUMN_LEN,
        \PDO::ATTR_EMULATE_PREPARES,
        \PDO::ATTR_DEFAULT_FETCH_MODE,
    ];

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
     * @param FilterGroup $group
     *
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
     * @param Query $query
     *
     * @throws QueryParameterException
     */
    public static function valueIsSelectQuery(Query $query): void
    {
        if (!$query instanceof Select) {
            throw QueryParameterException::notTypeOf('select query instance');
        }
    }

    /**
     * @param array $values
     * @param int   $number
     *
     * @throws QueryParameterException
     */
    public static function numberOfElements(array $values, int $number): void
    {
        if (count($values) !== $number) {
            throw QueryParameterException::numberOfElements($number);
        }
    }

    /**
     * @param string $operator
     * @param array  $operators
     *
     * @throws QueryParameterException
     */
    public static function validConditionOperator(string $operator, array $operators): void
    {
        if (!empty($operators) && !in_array($operator, $operators)) {
            throw QueryParameterException::unexpectedValue('condition', $operator);
        }
    }

    /**
     * @param string $operator
     *
     * @throws QueryParameterException
     */
    public static function validConjunctionOperator(string $operator): void
    {
        if (!in_array(strtolower($operator), ['and', 'or', ''])) {
            throw QueryParameterException::unexpectedValue('conjunction', $operator);
        }
    }

    /**
     * @param array  $joins
     * @param string $alias
     * @param string $hash
     *
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
     * @param array $returning
     *
     * @throws QueryRuntimeException
     */
    public static function returningConditionSet(array $returning): void
    {
        if (empty($returning)) {
            throw QueryRuntimeException::emptyReturningQueryPart();
        }
    }

    /**
     * @param array $set
     *
     * @throws QueryRuntimeException
     */
    public static function querySetPartNotEmpty(array $set): void
    {
        if (empty($set)) {
            throw QueryRuntimeException::emptySetQueryPart();
        }
    }

    /**
     * @param \PDOStatement|bool $statement
     *
     * @throws QueryRuntimeException
     */
    public static function queryExecuted($statement): void
    {
        if (!$statement instanceof \PDOStatement) {
            throw QueryRuntimeException::fetchBeforeExecute();
        }
    }

    /**
     * @param int $option
     *
     * @throws ServerException
     */
    public static function validConnectionOption(int $option): void
    {
        if (!in_array($option, self::CONNECTION_OPTIONS, true)) {
            throw ServerException::unexpectedConnectionOption();
        }
    }

    /**
     * @param Server $server
     * @param string $version
     *
     * @throws ServerException
     */
    public static function engineFeatureAvailable(Server $server, string $version): void
    {
        if ($server->getVersion() && version_compare($server->getVersion(), $version, '<')) {
            throw ServerException::engineFeatureNotAvailable($version);
        }
    }
}
