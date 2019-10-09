<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query\Condition;

use DateInterval;
use DateTime;
use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Query\Condition\BetweenCondition;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class BetweenConditionTest extends UnitTestCase
{
    use Escape;

    public function testConstructorWithInvalidPeriod(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Array must contains 2 elements');

        new BetweenCondition('foo', [(new DateTime())->format('Y-m-d H:i:s')]);
    }

    public function testConstructor(): void
    {
        $values = [
            (new DateTime())->format('Y-m-d H:i:s'),
            (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = new BetweenCondition('foo', $values);

        $expected = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($expected, 'values', $condition);
    }

    public function testCreate(): void
    {
        $values = [
            (new DateTime())->format('Y-m-d H:i:s'),
            (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = BetweenCondition::create('foo', $values);

        $expected = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        $this->assertPropertySame('foo', 'column', $condition);
        $this->assertPropertySame($expected, 'values', $condition);
    }

    public function testCompile(): void
    {
        $values = [
            (new DateTime())->format('Y-m-d H:i:s'),
            (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = BetweenCondition::create('foo', $values);

        [$begin, $end] = $values;

        $this->assertSame(sprintf("foo BETWEEN '%s' AND '%s'", $begin, $end), $condition->compile());
    }
}
