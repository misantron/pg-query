<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Filter;

use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class FilterGroupTest extends UnitTestCase
{
    public function testIsEmpty(): void
    {
        $group = new FilterGroup();

        $this->assertTrue($group->isEmpty());
    }

    public function testNotEmpty(): void
    {
        $group = new FilterGroup();

        $this->assertFalse($group->notEmpty());

        $group->append(Filter::create(new ValueCondition('foo', 5, '<')));

        $this->assertTrue($group->notEmpty());
    }

    public function testAppend(): void
    {
        $filter = Filter::create(new ValueCondition('foo', 5, '<'));

        $group = new FilterGroup();
        $group->append($filter);

        $this->assertAttributeEquals([$filter], 'list', $group);
    }

    public function testCompile(): void
    {
        $group = new FilterGroup();
        $group->append(Filter::create(new ValueCondition('foo', 5, '<')));
        $group->append(Filter::create(new NullCondition('bar', 'IS NOT'), 'OR'));

        $this->assertSame('foo < 5 OR bar IS NOT NULL', $group->compile());
    }
}
