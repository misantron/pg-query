<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\ArrayContainsCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ArrayContainsConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithEmptyValue(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        new ArrayContainsCondition('foo', []);
    }

    public function testConstructor(): void
    {
        $condition = new ArrayContainsCondition('foo', [3, 5, 8]);

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($this->escapeArray([3, 5, 8]), 'values', $condition);

        $condition = new ArrayContainsCondition('foo', ['bar', 'baz']);

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($this->escapeArray(['bar', 'baz']), 'values', $condition);
    }

    public function testCreate(): void
    {
        $condition = ArrayContainsCondition::create('foo', [3, 5, 8]);

        $this->assertEquals(new ArrayContainsCondition('foo', [3, 5, 8]), $condition);

        $condition = ArrayContainsCondition::create('foo', ['bar', 'baz']);

        $this->assertEquals(new ArrayContainsCondition('foo', ['bar', 'baz']), $condition);
    }

    public function testCompile(): void
    {
        $condition = new ArrayContainsCondition('foo', [3, 5, 8]);

        $this->assertSame("foo @> '{3,5,8}'", $condition->compile());

        $condition = new ArrayContainsCondition('foo', ['bar', 'baz']);

        $this->assertSame("foo @> '{\"bar\",\"baz\"}'", $condition->compile());
    }
}
