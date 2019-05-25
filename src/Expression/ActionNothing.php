<?php

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Compilable;

class ActionNothing implements Compilable
{
    /**
     * @return string
     */
    public function compile(): string
    {
        return 'NOTHING';
    }
}
