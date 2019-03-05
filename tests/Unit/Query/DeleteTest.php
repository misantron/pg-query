<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Delete;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class DeleteTest extends UnitTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(Server::class, 'server', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);
    }

    public function testBuildWithoutConditions()
    {
        $query = $this->createQuery();

        $this->assertSame('DELETE FROM foo.bar', $query->__toString());
    }

    public function testBuild()
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
        $query
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertSame("DELETE FROM foo.bar WHERE col1 = 1 AND col2 = 'test'", $query->__toString());
    }

    private function createQuery($server = null): Delete
    {
        $server = $server ?? $this->createServerMock();
        $table = 'foo.bar';

        return new Delete($server, $table);
    }
}
