<?php

namespace MediaTech\Query\Tests\Unit\Query\Filter;


use MediaTech\Query\Query\Condition\InCondition;
use MediaTech\Query\Query\Condition\NullCondition;
use MediaTech\Query\Query\Condition\ValueCondition;
use MediaTech\Query\Query\Filter\Filter;
use MediaTech\Query\Tests\BaseTestCase;

class FilterTest extends BaseTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid condition conjunction: unexpected value
     */
    public function testCreateWithInvalidConjunction()
    {
        $condition = new ValueCondition('bar', 5, '>');

        Filter::create($condition, 'foo');
    }

    public function testCreate()
    {
        $condition = new ValueCondition('bar', 5, '>');

        $filter = Filter::create($condition, 'AND');

        $this->assertEquals($condition, $filter->condition());
        $this->assertEquals('AND', $filter->conjunction());
        $this->assertEquals(false, $filter->group());
    }

    public function testCondition()
    {
        $condition = new NullCondition('bar', 'IS');

        $filter = Filter::create($condition, 'AND');

        $this->assertEquals($condition, $filter->condition());
    }

    public function testConjunction()
    {
        $condition = new InCondition('bar', [1,3], 'IN');

        $filter = Filter::create($condition, 'AND');

        $this->assertEquals('AND', $filter->conjunction());
    }

    public function testGroup()
    {
        $filter = Filter::create('(', 'AND', true);

        $this->assertEquals(true, $filter->group());
    }
}