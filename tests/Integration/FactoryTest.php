<?php

namespace Misantron\QueryBuilder\Tests\Integration;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query;

class FactoryTest extends IntegrationTestCase
{
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
        return Factory::create($this->getServer());
    }
}
