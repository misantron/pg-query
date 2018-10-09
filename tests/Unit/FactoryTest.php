<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query;

class FactoryTest extends UnitTestCase
{
    public function testConstructor()
    {
        $factory = $this->createFactory();
        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $factory);
    }

    public function testGetPDO()
    {
        $factory = $this->createFactory();

        $this->assertInstanceOf(\PDO::class, $factory->getPDO());
    }

    public function testInsert()
    {
        $factory = $this->createFactory();

        $query = $factory->insert('foo.bar');
        $this->assertInstanceOf(Query\Insert::class, $query);
    }

    public function testSelect()
    {
        $factory = $this->createFactory();

        $query = $factory->select('foo.bar');
        $this->assertInstanceOf(Query\Select::class, $query);
    }

    public function testUpdate()
    {
        $factory = $this->createFactory();

        $query = $factory->update('foo.bar');
        $this->assertInstanceOf(Query\Update::class, $query);
    }

    public function testDelete()
    {
        $factory = $this->createFactory();

        $query = $factory->delete('foo.bar');
        $this->assertInstanceOf(Query\Delete::class, $query);
    }

    private function createFactory()
    {
        return new Factory($this->createPDOMock());
    }
}