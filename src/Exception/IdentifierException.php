<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Exception;

use InvalidArgumentException;

/**
 * Class IdentifierException.
 */
final class IdentifierException extends InvalidArgumentException
{
    /**
     * @return IdentifierException
     */
    public static function supplyInvalidChar(): IdentifierException
    {
        return new static('Identifier supplied invalid characters');
    }

    /**
     * @return IdentifierException
     */
    public static function beginFromInvalidChar(): IdentifierException
    {
        return new static('Identifier must begin with a letter or underscore');
    }
}
