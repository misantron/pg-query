<?php

namespace Misantron\QueryBuilder\Tests\Integration;

use PDO;

class ServerTest extends IntegrationTestCase
{
    public function testVersion(): void
    {
        $this->assertSame('9.5', $this->getServer()->getVersion());
    }

    public function testPdo(): void
    {
        $this->assertSame(PDO::ERRMODE_EXCEPTION, $this->getServer()->pdo()->getAttribute(PDO::ATTR_ERRMODE));
    }
}
