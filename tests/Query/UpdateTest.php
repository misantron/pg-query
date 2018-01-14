<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Query\Update;
use MediaTech\Query\Tests\BaseTestCase;

class UpdateTest extends BaseTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);
        $this->assertAttributeEquals(null, 'set', $query);
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

        $this->assertEquals('UPDATE foo.bar SET col1 = 1', $query->build());
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

        $this->assertEquals("UPDATE foo.bar SET foo = 'bar' WHERE col1 = 1 AND col2 = 'test'", $query->build());
    }

    private function createQuery($pdo = null)
    {
        $pdo = $pdo ?? $this->createPDOMock();
        $table = 'foo.bar';

        return new Update($pdo, $table);
    }
}