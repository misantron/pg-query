<?php

namespace MediaTech\Query\Tests\Unit\Query\Condition;


use MediaTech\Query\Helper\Escape;
use MediaTech\Query\Query\Condition\ArrayContainsCondition;
use MediaTech\Query\Tests\BaseTestCase;

class ArrayContainsConditionTest extends BaseTestCase
{
    use Escape;

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid condition value: value list is empty
     */
    public function testConstructorWithEmptyValue()
    {
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

        $this->assertEquals('foo @> ARRAY[3,5,8]::INTEGER[]', $condition->__toString());
        $this->assertEquals('foo @> ARRAY[3,5,8]::INTEGER[]', (string)$condition);

        $condition = new ArrayContainsCondition('foo', ['bar', 'baz']);

        $this->assertEquals("foo @> ARRAY['bar','baz']::VARCHAR[]", $condition->__toString());
        $this->assertEquals("foo @> ARRAY['bar','baz']::VARCHAR[]", (string)$condition);
    }
}