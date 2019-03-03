<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Filter;

use Misantron\QueryBuilder\Query\Condition\InCondition;
use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class FilterTest extends UnitTestCase
{
    public function testCreateWithInvalidConjunction()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid condition conjunction: unexpected value');

        $condition = new ValueCondition('bar', 5, '>');

        Filter::create($condition, 'foo');
    }

    public function testCreate()
    {
        $condition = new ValueCondition('bar', 5, '>');

        $filter = Filter::create($condition, 'AND');

        $this->assertEquals($condition, $filter->condition());
        $this->assertSame('AND', $filter->conjunction());
        $this->assertSame(false, $filter->group());
    }

    public function testCondition()
    {
        $condition = new NullCondition('bar', 'IS');

        $filter = Filter::create($condition, 'AND');

        $this->assertEquals($condition, $filter->condition());
    }

    public function testConjunction()
    {
        $condition = new InCondition('bar', [1, 3], 'IN');

        $filter = Filter::create($condition, 'AND');

        $this->assertSame('AND', $filter->conjunction());
    }

    public function testGroup()
    {
        $filter = Filter::create('(', 'AND', true);

        $this->assertSame(true, $filter->group());
    }
}
