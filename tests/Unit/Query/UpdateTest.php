<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;
use PDO;

class UpdateTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $query = $this->createQuery();

        $this->assertPropertyInstanceOf(Server::class, 'server', $query);
        $this->assertPropertyInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertPropertyNull('table', $query);
        $this->assertPropertySame([], 'set', $query);
    }

    public function testSetWithEmptyData(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        $query = $this->createQuery();
        $query->set([]);
    }

    public function testSet(): void
    {
        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['test', PDO::PARAM_STR])
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

        $this->assertPropertySame(['foo' => '1', 'bar' => "'test'"], 'set', $query);
    }

    public function testCompileWithoutConditions(): void
    {
        $query = $this->createQuery();
        $query->table('foo.bar');
        $query->set(['col1' => 1]);

        $this->assertSame('UPDATE foo.bar SET col1 = 1', $query->compile());
    }

    public function testCompileWithoutSet(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query set must be filled');

        $query = $this->createQuery();
        $query->andEquals('col1', 1);

        $query->compile();
    }

    public function testCompile(): void
    {
        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['bar', PDO::PARAM_STR], ['test', PDO::PARAM_STR])
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

        $this->assertSame("UPDATE foo.bar SET foo = 'bar' WHERE col1 = 1 AND col2 = 'test'", $query->compile());
    }

    private function createQuery($server = null): Update
    {
        $server = $server ?? $this->createServerMock();

        return new Update($server);
    }
}
