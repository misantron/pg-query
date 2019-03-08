<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query;
use Misantron\QueryBuilder\Server;

class FactoryTest extends UnitTestCase
{
    public function testGetServer()
    {
        $factory = $this->createFactory();

        $this->assertInstanceOf(Server::class, $factory->getServer());
    }

    public function testSetServer()
    {
        $factory = $this->createFactory();
        $currentServer = $factory->getServer();

        $server = $this->createServerMock();
        $factory->setServer($server);

        $this->assertNotSame($currentServer, $factory->getServer());
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

        $query = $factory->update();
        $this->assertInstanceOf(Query\Update::class, $query);
    }

    public function testDelete()
    {
        $factory = $this->createFactory();

        $query = $factory->delete('foo.bar');
        $this->assertInstanceOf(Query\Delete::class, $query);
    }

    /**
     * @return Factory
     */
    private function createFactory(): Factory
    {
        return Factory::create($this->createServerMock());
    }
}
