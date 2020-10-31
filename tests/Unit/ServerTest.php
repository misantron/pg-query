<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Exception\ServerException;
use Misantron\QueryBuilder\Server;

class ServerTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $server = new Server([], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

        self::assertPropertySame(['host' => 'localhost', 'port' => '5432'], 'credentials', $server);
        self::assertPropertySame([\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION], 'options', $server);
        self::assertPropertyNull('version', $server);
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
        self::assertPropertySame([], 'options', $server);

        $server->setOption(\PDO::ATTR_EMULATE_PREPARES, true);
        self::assertPropertySame([\PDO::ATTR_EMULATE_PREPARES => true], 'options', $server);
    }

    public function testGetVersion(): void
    {
        $server = new Server([], []);
        self::assertNull($server->getVersion());

        $server = new Server([], [], '9.6');
        self::assertSame('9.6', $server->getVersion());
    }

    public function testPdoWithConnectionException(): void
    {
        $server = new Server(
            [
                'dbname' => 'postgres',
                'user' => 'user',
                'password' => 'password',
            ],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
            ]
        );

        try {
            $pdo = $server->pdo();
        } catch (\PDOException $e) {
            $pdo = null;
        }

        self::assertNull($pdo);
    }
}
