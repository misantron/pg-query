<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Compilable;

final class ActionNothing implements Compilable
{
    public function compile(): string
    {
        return 'NOTHING';
    }
}
