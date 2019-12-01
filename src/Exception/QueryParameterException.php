<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use InvalidArgumentException;

/**
 * Class QueryParameterException.
 */
final class QueryParameterException extends InvalidArgumentException
{
    public static function emptyValue(string $name): QueryParameterException
    {
        return new static("{$name} is empty");
    }

    public static function unexpectedValue(string $type, string $value): QueryParameterException
    {
        return new static("Invalid {$type} - unexpected value: {$value}");
    }

    public static function unexpectedValues(string $type, array $values): QueryParameterException
    {
        return new static("Invalid {$type} - unexpected value: {$values}");
    }

    public static function notTypeOf(string $type): QueryParameterException
    {
        return new static("Value must be a {$type}");
    }

    public static function numberOfElements(int $number): QueryParameterException
    {
        return new static("Array must contains {$number} elements");
    }

    public static function encodingError(string $error): QueryParameterException
    {
        return new static("Value encoding error: {$error}");
    }
}
