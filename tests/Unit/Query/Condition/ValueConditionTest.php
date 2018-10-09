<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ValueConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructor()
    {
        $condition = new ValueCondition('foo', 'bar', '=');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeValue('bar'), 'value', $condition);
        $this->assertAttributeEquals('=', 'operator', $condition);

        $condition = new ValueCondition('foo', 5, '>=');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals(5, 'value', $condition);
        $this->assertAttributeEquals('>=', 'operator', $condition);
    }

    public function testCreate()
    {
        $condition = ValueCondition::create('foo', 'bar', '=');

        $this->assertInstanceOf(ValueCondition::class, $condition);
        $this->assertEquals(new ValueCondition('foo', 'bar', '='), $condition);
    }

    public function testToString()
    {
        $condition = new ValueCondition('foo', 'bar', '=');

        $this->assertEquals("foo = 'bar'", $condition->__toString());
        $this->assertEquals("foo = 'bar'", (string)$condition);

        $condition = new ValueCondition('foo', 5, '>=');

        $this->assertEquals('foo >= 5', $condition->__toString());
        $this->assertEquals('foo >= 5', (string)$condition);
    }
}