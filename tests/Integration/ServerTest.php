<?php

namespace Misantron\QueryBuilder\Tests\Integration;

class ServerTest extends IntegrationTestCase
{
    public function testVersion()
    {
        $this->assertSame('9.5', $this->getServer()->getVersion());
    }

    public function testPdo()
    {
        $this->assertInstanceOf(\PDO::class, $this->getServer()->pdo());
        $this->assertSame(\PDO::ERRMODE_EXCEPTION, $this->getServer()->pdo()->getAttribute(\PDO::ATTR_ERRMODE));
    }
}
