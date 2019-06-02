<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Server;

class ServerTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $server = new Server([], [], '9.5');

        $this->assertAttributeSame(['host' => 'localhost', 'port' => '5432'], 'credentials', $server);
        $this->assertAttributeSame([], 'options', $server);
        $this->assertAttributeSame('9.5', 'version', $server);
    }
}
