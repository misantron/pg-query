<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Compilable;

final class ActionNothing implements Compilable
{
    /**
     * @return string
     */
    public function compile(): string
    {
        return 'NOTHING';
    }
}
