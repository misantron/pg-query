<?php

namespace MediaTech\Query\Tests\Unit\Query\Condition;


use MediaTech\Query\Helper\Escape;
use MediaTech\Query\Query\Condition\InArrayCondition;
use MediaTech\Query\Tests\BaseTestCase;

class InArrayConditionTest extends BaseTestCase
{
    use Escape;

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid condition operator: unexpected value
     */
    public function testConstructorWithInvalidOperator()
    {
        new InArrayCondition('foo', 3, '>');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value: value must be a scalar
     */
    public function testConstructorWithNotScalarValue()
    {
        new InArrayCondition('foo', [], '=');
    }

    public function testConstructor()
    {
        $condition = new InArrayCondition('foo', 5, '=');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals(5, 'value', $condition);
        $this->assertAttributeEquals('=', 'operator', $condition);

        $condition = new InArrayCondition('foo', 'bar', '!=');

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($this->escapeValue('bar'), 'value', $condition);
        $this->assertAttributeEquals('!=', 'operator', $condition);
    }

    public function testCreate()
    {
        $condition = InArrayCondition::create('foo', 5, '=');

        $this->assertEquals($condition, new InArrayCondition('foo', 5, '='));
    }

    public function testToString()
    {
        $condition = new InArrayCondition('foo', 5, '=');

        $this->assertEquals("5 = ANY(foo)", $condition->__toString());
        $this->assertEquals("5 = ANY(foo)", (string)$condition);

        $condition = new InArrayCondition('foo', 'bar', '!=');

        $this->assertEquals("'bar' != ANY(foo)", $condition->__toString());
        $this->assertEquals("'bar' != ANY(foo)", (string)$condition);
    }
}