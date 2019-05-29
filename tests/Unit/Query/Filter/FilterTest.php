<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query\Filter;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Query\Condition\InCondition;
use Misantron\QueryBuilder\Query\Condition\NullCondition;
use Misantron\QueryBuilder\Query\Condition\ValueCondition;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class FilterTest extends UnitTestCase
{
    public function testCreateWithInvalidConjunction(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Invalid conjunction - unexpected value: foo');

        $condition = new ValueCondition('bar', 5, '>');

        Filter::create($condition, 'foo');
    }

    public function testCreate(): void
    {
        $condition = new ValueCondition('bar', 5, '>');

        $filter = Filter::create($condition, 'AND');

        $this->assertSame($condition->compile(), $filter->condition());
        $this->assertSame('AND', $filter->conjunction());
        $this->assertFalse($filter->group());
    }

    public function testCondition(): void
    {
        $condition = new NullCondition('bar', 'IS');

        $filter = Filter::create($condition, 'AND');

        $this->assertSame($condition->compile(), $filter->condition());
    }

    public function testConjunction(): void
    {
        $condition = new InCondition('bar', [1, 3], 'IN');

        $filter = Filter::create($condition, 'AND');

        $this->assertSame('AND', $filter->conjunction());
    }

    public function testGroup(): void
    {
        $filter = Filter::create('(', 'AND', true);

        $this->assertTrue($filter->group());
    }
}
