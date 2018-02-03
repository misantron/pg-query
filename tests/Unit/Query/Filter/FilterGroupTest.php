<?php

namespace MediaTech\Query\Tests\Unit\Query\Filter;


use MediaTech\Query\Query\Condition\NullCondition;
use MediaTech\Query\Query\Condition\ValueCondition;
use MediaTech\Query\Query\Filter\Filter;
use MediaTech\Query\Query\Filter\FilterGroup;
use MediaTech\Query\Tests\Unit\BaseTestCase;

class FilterGroupTest extends BaseTestCase
{
    public function testNotEmpty()
    {
        $group = new FilterGroup();

        $this->assertFalse($group->notEmpty());

        $group->append(Filter::create(new ValueCondition('foo', 5, '<')));

        $this->assertTrue($group->notEmpty());
    }

    public function testAppend()
    {
        $filter = Filter::create(new ValueCondition('foo', 5, '<'));

        $group = new FilterGroup();
        $group->append($filter);

        $this->assertAttributeEquals([$filter], 'list', $group);
    }

    public function testToString()
    {
        $group = new FilterGroup();
        $group->append(Filter::create(new ValueCondition('foo', 5, '<')));
        $group->append(Filter::create(new NullCondition('bar', 'IS NOT'), 'OR'));

        $this->assertEquals('foo < 5 OR bar IS NOT NULL', $group->__toString());
        $this->assertEquals('foo < 5 OR bar IS NOT NULL', (string)$group);
    }
}