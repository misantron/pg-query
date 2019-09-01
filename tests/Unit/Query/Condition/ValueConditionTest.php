<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ValueConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructor(): void
    {
        $condition = new ValueCondition('foo', 'bar', '=');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($this->escapeValue('bar'), 'value', $condition);
        $this->assertPropertySame('=', 'operator', $condition);

        $condition = new ValueCondition('foo', 5, '>=');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame('5', 'value', $condition);
        $this->assertPropertySame('>=', 'operator', $condition);
    }

    public function testCreate(): void
    {
        $condition = ValueCondition::create('foo', 'bar', '=');

        $this->assertEquals(new ValueCondition('foo', 'bar', '='), $condition);
    }

    public function testCompile(): void
    {
        $condition = new ValueCondition('foo', 'bar', '=');

        $this->assertSame("foo = 'bar'", $condition->compile());

        $condition = new ValueCondition('foo', 5, '>=');

        $this->assertSame('foo >= 5', $condition->compile());
    }
}
