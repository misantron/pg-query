<?php

namespace MediaTech\Query\Tests\Unit;


use MediaTech\Query\Factory;
use MediaTech\Query\Query\Delete;
use MediaTech\Query\Query\Insert;
use MediaTech\Query\Query\Select;
use MediaTech\Query\Query\Update;

class FactoryTest extends BaseTestCase
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
        $this->assertInstanceOf(Insert::class, $query);
    }

    public function testSelect()
    {
        $factory = $this->createFactory();

        $query = $factory->select('foo.bar');
        $this->assertInstanceOf(Select::class, $query);
    }

    public function testUpdate()
    {
        $factory = $this->createFactory();

        $query = $factory->update('foo.bar');
        $this->assertInstanceOf(Update::class, $query);
    }

    public function testDelete()
    {
        $factory = $this->createFactory();

        $query = $factory->delete('foo.bar');
        $this->assertInstanceOf(Delete::class, $query);
    }

    private function createFactory()
    {
        return new Factory($this->createPDOMock());
    }
}