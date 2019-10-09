<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ConflictTargetTest extends UnitTestCase
{
    public function testCreateFromField(): void
    {
        $target = ConflictTarget::fromField('foo');

        $this->assertPropertySame('(foo)', 'expr', $target);
    }

    public function testCreateFromConstraint(): void
    {
        $target = ConflictTarget::fromConstraint('foo_bar_key');

        $this->assertPropertySame('ON CONSTRAINT foo_bar_key', 'expr', $target);
    }

    public function testCreateFromEmptyCondition(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Condition is empty');

        $condition = new FilterGroup();

        ConflictTarget::fromCondition($condition);
    }

    public function testCreateFromCondition(): void
    {
        $condition = new FilterGroup();
        $condition->append(Filter::create('foo > 5'));

        $target = ConflictTarget::fromCondition($condition);

        $this->assertPropertySame('WHERE foo > 5', 'expr', $target);
    }

    public function testCompile(): void
    {
        $target = ConflictTarget::fromConstraint('foo_bar_key');

        $this->assertSame('ON CONSTRAINT foo_bar_key', $target->compile());
    }
}
