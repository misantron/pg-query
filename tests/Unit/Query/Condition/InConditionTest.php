<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\InCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class InConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithEmptyValueList()
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        new InCondition('foo', [''], 'IN');
    }

    public function testConstructor()
    {
        $condition = new InCondition('foo', [1, 2, 3], 'IN');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals([1, 2, 3], 'values', $condition);
        $this->assertAttributeEquals('IN', 'operator', $condition);

        $condition = new InCondition('foo', ['bar', 'baz'], 'NOT IN');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeList(['bar', 'baz']), 'values', $condition);
        $this->assertAttributeEquals('NOT IN', 'operator', $condition);
    }

    public function testCreate()
    {
        $condition = InCondition::create('foo', [1, 2, 3], 'NOT IN');

        $this->assertEquals(new InCondition('foo', [1, 2, 3], 'NOT IN'), $condition);
    }

    public function testToString()
    {
        $condition = new InCondition('foo', [1, 2, 3], 'IN');

        $this->assertSame('foo IN (1,2,3)', $condition->__toString());
        $this->assertSame('foo IN (1,2,3)', (string)$condition);

        $condition = new InCondition('foo', ['bar', 'baz'], 'NOT IN');

        $this->assertSame("foo NOT IN ('bar','baz')", $condition->__toString());
        $this->assertSame("foo NOT IN ('bar','baz')", (string)$condition);
    }
}
