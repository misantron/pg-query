<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Query\Query;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class QueryTest extends UnitTestCase
{
    public function testTable()
    {
        $pdo = $this->createPDOMock();

        $query = new class($pdo) extends Query
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