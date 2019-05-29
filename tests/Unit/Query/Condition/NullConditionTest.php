<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class NullConditionTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $condition = new NullCondition('foo', 'IS');

        $this->assertAttributeSame('foo', 'column', $condition);
        $this->assertAttributeSame('IS', 'operator', $condition);

        $condition = new NullCondition('foo', 'IS NOT');

        $this->assertAttributeSame('foo', 'column', $condition);
        $this->assertAttributeSame('IS NOT', 'operator', $condition);
    }

    public function testCreate(): void
    {
        $condition = NullCondition::create('foo');

        $this->assertEquals(new NullCondition('foo', 'IS'), $condition);

        $condition = new NullCondition('foo', 'IS NOT');

        $this->assertEquals(new NullCondition('foo', 'IS NOT'), $condition);
    }

    public function testCompile(): void
    {
        $condition = new NullCondition('foo', 'IS');

        $this->assertSame('foo IS NULL', $condition->compile());

        $condition = new NullCondition('foo', 'IS NOT');

        $this->assertSame('foo IS NOT NULL', $condition->compile());
    }
}
