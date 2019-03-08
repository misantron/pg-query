<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\ArrayContainsCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ArrayContainsConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithEmptyValue()
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        new ArrayContainsCondition('foo', []);
    }

    public function testConstructor()
    {
        $condition = new ArrayContainsCondition('foo', [3, 5, 8]);

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeArray([3, 5, 8]), 'values', $condition);

        $condition = new ArrayContainsCondition('foo', ['bar', 'baz']);

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeArray(['bar', 'baz']), 'values', $condition);
    }

    public function testCreate()
    {
        $condition = ArrayContainsCondition::create('foo', [3, 5, 8]);

        $this->assertEquals(new ArrayContainsCondition('foo', [3, 5, 8]), $condition);

        $condition = ArrayContainsCondition::create('foo', ['bar', 'baz']);

        $this->assertEquals(new ArrayContainsCondition('foo', ['bar', 'baz']), $condition);
    }

    public function testToString()
    {
        $condition = new ArrayContainsCondition('foo', [3, 5, 8]);

        $this->assertSame('foo @> ARRAY[3,5,8]::INTEGER[]', $condition->__toString());
        $this->assertSame('foo @> ARRAY[3,5,8]::INTEGER[]', (string)$condition);

        $condition = new ArrayContainsCondition('foo', ['bar', 'baz']);

        $this->assertSame("foo @> ARRAY['bar','baz']::VARCHAR[]", $condition->__toString());
        $this->assertSame("foo @> ARRAY['bar','baz']::VARCHAR[]", (string)$condition);
    }
}