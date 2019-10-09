<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query;
use Misantron\QueryBuilder\Server;

class FactoryTest extends UnitTestCase
{
    public function testGetServer(): void
    {
        $factory = $this->createFactory();

        $this->assertInstanceOf(Server::class, $factory->getServer());
    }

    public function testSetServer(): void
    {
        $factory = $this->createFactory();
        $currentServer = $factory->getServer();

        $server = $this->createServerMock();
        $factory->setServer($server);

        $this->assertNotSame($currentServer, $factory->getServer());
    }

    public function testInsert(): void
    {
        $factory = $this->createFactory();

        $query = $factory->insert('foo.bar');
        $this->assertInstanceOf(Query\Insert::class, $query);
    }

    public function testSelect(): void
    {
        $factory = $this->createFactory();

        $query = $factory->select('foo.bar');
        $this->assertInstanceOf(Query\Select::class, $query);
    }

    public function testUpdate(): void
    {
        $factory = $this->createFactory();

        $query = $factory->update();
        $this->assertInstanceOf(Query\Update::class, $query);
    }

    public function testDelete(): void
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
