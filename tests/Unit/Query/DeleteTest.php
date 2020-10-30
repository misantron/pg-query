<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Delete;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class DeleteTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $query = $this->createQuery();

        $this->assertPropertyInstanceOf(Server::class, 'server', $query);
        $this->assertPropertyInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertPropertySame('foo.bar', 'table', $query);
    }

    public function testBuildWithoutConditions(): void
    {
        $query = $this->createQuery();

        $this->assertSame('DELETE FROM foo.bar', $query->compile());
    }

    public function testCompile(): void
    {
        $pdo = $this->createPDOMock();
        $server = $this->createServerMock($pdo);

        $query = $this->createQuery($server);
        $query
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertSame("DELETE FROM foo.bar WHERE col1 = 1 AND col2 = 'test'", $query->compile());
    }

    private function createQuery($server = null): Delete
    {
        $server = $server ?? $this->createServerMock();
        $table = 'foo.bar';

        return new Delete($server, $table);
    }
}
