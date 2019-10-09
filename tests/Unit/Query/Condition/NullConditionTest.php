<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class NullConditionTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $condition = new NullCondition('foo', 'IS');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame('IS', 'operator', $condition);

        $condition = new NullCondition('foo', 'IS NOT');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame('IS NOT', 'operator', $condition);
    }

    public function testCreate(): void
    {
        $condition = NullCondition::create('foo', 'IS');

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
