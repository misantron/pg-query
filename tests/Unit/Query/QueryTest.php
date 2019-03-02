<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Query;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class QueryTest extends UnitTestCase
{
    public function testTable()
    {
        $server = $this->createServerMock();

        $query = new class($server) extends Query
        {
            public function __toString(): string
            {
                throw new \BadMethodCallException('Not implemented');
            }
        };
        $this->assertAttributeSame(null, 'table', $query);

        $query->table('foo.bar');
        $this->assertAttributeSame('foo.bar', 'table', $query);
    }
}