<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Query\Filter\FilterGroup;
use MediaTech\Query\Query\Update;
use MediaTech\Query\Tests\BaseTestCase;

class UpdateTest extends BaseTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);
        $this->assertAttributeEquals([], 'set', $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value list is empty
     */
    public function testSetWithEmptyData()
    {
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
        $query->set(['col1' => 1]);

        $this->assertEquals('UPDATE foo.bar SET col1 = 1', $query->__toString());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Query set is empty
     */
    public function testBuildWithoutSet()
    {
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
            ->set(['foo' => 'bar'])
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertEquals("UPDATE foo.bar SET foo = 'bar' WHERE col1 = 1 AND col2 = 'test'", $query->__toString());
    }

    private function createQuery($pdo = null)
    {
        $pdo = $pdo ?? $this->createPDOMock();
        $table = 'foo.bar';

        return new Update($pdo, $table);
    }
}