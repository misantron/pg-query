<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\InArrayCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class InArrayConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithInvalidOperator(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Invalid condition - unexpected value: >');

        new InArrayCondition('foo', 3, '>');
    }

    public function testConstructorWithNotScalarValue(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value must be a scalar');

        new InArrayCondition('foo', [], '=');
    }

    public function testConstructor(): void
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

    public function testCreate(): void
    {
        $condition = InArrayCondition::create('foo', 5, '=');

        $this->assertEquals($condition, new InArrayCondition('foo', 5, '='));
    }

    public function testCompile(): void
    {
        $condition = new InArrayCondition('foo', 5, '=');

        $this->assertSame('5 = ANY(foo)', $condition->compile());

        $condition = new InArrayCondition('foo', 'bar', '!=');

        $this->assertSame("'bar' != ANY(foo)", $condition->compile());
    }
}
