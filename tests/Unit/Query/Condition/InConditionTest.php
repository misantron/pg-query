<?php

namespace MediaTech\Query\Tests\Unit\Query\Condition;


use MediaTech\Query\Helper\Escape;
use MediaTech\Query\Query\Condition\InCondition;
use MediaTech\Query\Tests\Unit\BaseTestCase;

class InConditionTest extends BaseTestCase
{
    use Escape;

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid values: value list is empty
     */
    public function testConstructorWithEmptyValueList()
    {
        new InCondition('foo', [''], 'IN');
    }

    public function testConstructor()
    {
        $condition = new InCondition('foo', [1,2,3], 'IN');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals([1,2,3], 'values', $condition);
        $this->assertAttributeEquals('IN', 'operator', $condition);

        $condition = new InCondition('foo', ['bar','baz'], 'NOT IN');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeList(['bar','baz']), 'values', $condition);
        $this->assertAttributeEquals('NOT IN', 'operator', $condition);
    }

    public function testCreate()
    {
        $condition = InCondition::create('foo', [1,2,3], 'NOT IN');

        $this->assertEquals(new InCondition('foo', [1,2,3], 'NOT IN'), $condition);
    }

    public function testToString()
    {
        $condition = new InCondition('foo', [1,2,3], 'IN');

        $this->assertEquals('foo IN (1,2,3)', $condition->__toString());
        $this->assertEquals('foo IN (1,2,3)', (string)$condition);

        $condition = new InCondition('foo', ['bar','baz'], 'NOT IN');

        $this->assertEquals("foo NOT IN ('bar','baz')", $condition->__toString());
        $this->assertEquals("foo NOT IN ('bar','baz')", (string)$condition);
    }
}