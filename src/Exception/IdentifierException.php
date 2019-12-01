<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use InvalidArgumentException;

/**
 * Class IdentifierException.
 */
final class IdentifierException extends InvalidArgumentException
{
    public static function supplyInvalidChar(): IdentifierException
    {
        return new static('Identifier supplied invalid characters');
    }

    public static function beginFromInvalidChar(): IdentifierException
    {
        return new static('Identifier must begin with a letter or underscore');
    }
}
