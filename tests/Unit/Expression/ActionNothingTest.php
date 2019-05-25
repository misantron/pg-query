<?php

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Expression\ActionNothing;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ActionNothingTest extends UnitTestCase
{
    public function testCompile(): void
    {
        $action = new ActionNothing();

        $this->assertSame('NOTHING', $action->compile());
    }
}
