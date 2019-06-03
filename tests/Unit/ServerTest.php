<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Exception\ServerException;
use Misantron\QueryBuilder\Server;
use PDO;

class ServerTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $server = new Server([], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $this->assertAttributeSame(['host' => 'localhost', 'port' => '5432'], 'credentials', $server);
        $this->assertAttributeSame([PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION], 'options', $server);
        $this->assertAttributeSame(null, 'version', $server);
    }

    public function testSetOptionWithInvalidValue(): void
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Unexpected connection option provided');

        $server = new Server([], []);
        $server->setOption(100500, true);
    }

    public function testSetOption(): void
    {
        $server = new Server([], []);
        $this->assertAttributeSame([], 'options', $server);

        $server->setOption(PDO::ATTR_EMULATE_PREPARES, true);
        $this->assertAttributeSame([PDO::ATTR_EMULATE_PREPARES => true], 'options', $server);
    }

    public function testGetVersion(): void
    {
        $server = new Server([], []);
        $this->assertNull($server->getVersion());

        $server = new Server([], [], '9.6');
        $this->assertSame('9.6', $server->getVersion());
    }
}
