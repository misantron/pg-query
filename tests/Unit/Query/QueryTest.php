<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use BadMethodCallException;
use Misantron\QueryBuilder\Query\Query;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class QueryTest extends UnitTestCase
{
    public function testTable(): void
    {
        $server = $this->createServerMock();

        $query = new class ($server) extends Query {
            public function compile(): string
            {
                throw new BadMethodCallException('Not implemented');
            }
        };
        $this->assertPropertyNull('table', $query);

        $query->table('foo.bar');
        $this->assertPropertySame('foo.bar', 'table', $query);
    }
}
