<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class UpdateTest extends UnitTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(Server::class, 'server', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeSame(null, 'table', $query);
        $this->assertAttributeSame([], 'set', $query);
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

        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        $server
            ->method('pdo')
            ->willReturn($pdo);

        $query = $this->createQuery($server);
        $query->set($set = [
            'foo' => 1,
            'bar' => 'test',
        ]);

        $this->assertAttributeEquals(['foo' => 1, 'bar' => "'test'"], 'set', $query);
    }

    public function testBuildWithoutConditions()
    {
        $query = $this->createQuery();
        $query->table('foo.bar');
        $query->set(['col1' => 1]);

        $this->assertSame('UPDATE foo.bar SET col1 = 1', $query->__toString());
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

        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        $server
            ->method('pdo')
            ->willReturn($pdo);

        $query = $this->createQuery($server);
        $query
            ->table('foo.bar')
            ->set(['foo' => 'bar'])
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertSame("UPDATE foo.bar SET foo = 'bar' WHERE col1 = 1 AND col2 = 'test'", $query->__toString());
    }

    private function createQuery($server = null): Update
    {
        $server = $server ?? $this->createServerMock();

        return new Update($server);
    }
}
