<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\InCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class InConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithEmptyValueList(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        new InCondition('foo', [''], 'IN');
    }

    public function testConstructor(): void
    {
        $condition = new InCondition('foo', [1, 2, 3], 'IN');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame([1, 2, 3], 'values', $condition);
        $this->assertPropertySame('IN', 'operator', $condition);

        $condition = new InCondition('foo', ['bar', 'baz'], 'NOT IN');

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($this->escapeList(['bar', 'baz']), 'values', $condition);
        $this->assertPropertySame('NOT IN', 'operator', $condition);
    }

    public function testCreate(): void
    {
        $condition = InCondition::create('foo', [1, 2, 3], 'NOT IN');

        $this->assertEquals(new InCondition('foo', [1, 2, 3], 'NOT IN'), $condition);
    }

    public function testCompile(): void
    {
        $condition = new InCondition('foo', [1, 2, 3], 'IN');

        $this->assertSame('foo IN (1,2,3)', $condition->compile());

        $condition = new InCondition('foo', ['bar', 'baz'], 'NOT IN');

        $this->assertSame("foo NOT IN ('bar','baz')", $condition->compile());
    }
}
