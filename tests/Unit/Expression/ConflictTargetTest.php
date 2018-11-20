<?php

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Query\Filter\Filter;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class ConflictTargetTest extends UnitTestCase
{
    public function testCreateFromField()
    {
        $target = ConflictTarget::fromField('foo');

        $this->assertAttributeSame('(foo)', 'expr', $target);
    }

    public function testCreateFromConstraint()
    {
        $target = ConflictTarget::fromConstraint('foo_bar_key');

        $this->assertAttributeSame('ON CONSTRAINT foo_bar_key', 'expr', $target);
    }

    public function testCreateFromEmptyCondition()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Condition is empty');

        $condition = new FilterGroup();

        ConflictTarget::fromCondition($condition);
    }

    public function testCreateFromCondition()
    {
        $condition = new FilterGroup();
        $condition->append(Filter::create('foo > 5'));

        $target = ConflictTarget::fromCondition($condition);

        $this->assertAttributeSame('WHERE foo > 5', 'expr', $target);
    }

    public function testToString()
    {
        $target = ConflictTarget::fromConstraint('foo_bar_key');

        $this->assertSame('ON CONSTRAINT foo_bar_key', (string)$target);
    }
}