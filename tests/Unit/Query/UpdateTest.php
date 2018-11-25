<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class UpdateTest extends UnitTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeEquals(null, 'table', $query);
        $this->assertAttributeEquals([], 'set', $query);
    }

    public function testSetWithEmptyData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value list is empty');

        $query = $this->createQuery();
        $query->set([]);
    }

    public function testSet()
    {
        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['test', \PDO::PARAM_STR])
            ->willReturnOnConsecutiveCalls("'test'");

        $query = $this->createQuery($pdo);
        $query->set($set = [
            'foo' => 1,
            'bar' => 'test'
        ]);

        $this->assertAttributeEquals(['foo' => 1, 'bar' => "'test'"], 'set', $query);
    }

    public function testBuildWithoutConditions()
    {
        $query = $this->createQuery();
        $query->table('foo.bar');
        $query->set(['col1' => 1]);

        $this->assertEquals('UPDATE foo.bar SET col1 = 1', $query->__toString());
    }

    public function testBuildWithoutSet()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Query set is empty');

        $query = $this->createQuery();
        $query->andEquals('col1', 1);

        $query->__toString();
    }

    public function testBuild()
    {
        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['bar', \PDO::PARAM_STR], ['test', \PDO::PARAM_STR])
            ->willReturnOnConsecutiveCalls("'bar'", "'test'");

        $query = $this->createQuery($pdo);
        $query
            ->table('foo.bar')
            ->set(['foo' => 'bar'])
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertEquals("UPDATE foo.bar SET foo = 'bar' WHERE col1 = 1 AND col2 = 'test'", $query->__toString());
    }

    /**
     * @param \PDO|null $pdo
     *
     * @return Update
     */
    private function createQuery($pdo = null): Update
    {
        $pdo = $pdo ?? $this->createPDOMock();

        return new Update($pdo);
    }
}