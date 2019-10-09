<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use InvalidArgumentException;

/**
 * Class QueryParameterException.
 */
final class QueryParameterException extends InvalidArgumentException
{
    /**
     * @param string $name
     *
     * @return QueryParameterException
     */
    public static function emptyValue(string $name): QueryParameterException
    {
        return new static("{$name} is empty");
    }

    /**
     * @param string $type
     * @param string $value
     *
     * @return QueryParameterException
     */
    public static function unexpectedValue(string $type, string $value): QueryParameterException
    {
        return new static("Invalid {$type} - unexpected value: {$value}");
    }

    /**
     * @param string $type
     * @param array  $values
     *
     * @return QueryParameterException
     */
    public static function unexpectedValues(string $type, array $values): QueryParameterException
    {
        return new static("Invalid {$type} - unexpected value: {$values}");
    }

    /**
     * @param string $type
     *
     * @return QueryParameterException
     */
    public static function notTypeOf(string $type): QueryParameterException
    {
        return new static("Value must be a {$type}");
    }

    /**
     * @param int $number
     *
     * @return QueryParameterException
     */
    public static function numberOfElements(int $number): QueryParameterException
    {
        return new static("Array must contains {$number} elements");
    }

    /**
     * @param string $error
     *
     * @return QueryParameterException
     */
    public static function encodingError(string $error): QueryParameterException
    {
        return new static("Value encoding error: {$error}");
    }
}
