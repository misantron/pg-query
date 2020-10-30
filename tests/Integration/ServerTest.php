<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Integration;

class ServerTest extends IntegrationTestCase
{
    public function testConstructor(): void
    {
        $expectedCredentials = [
            'host' => 'localhost',
            'port' => '5432',
            'dbname' => 'postgres',
            'user' => 'postgres',
            'password' => 'postgres',
        ];
        $expectedOptions = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->assertPropertySame($expectedCredentials, 'credentials', $this->getServer());
        $this->assertPropertySame($expectedOptions, 'options', $this->getServer());
        $this->assertPropertySame('9.5', 'version', $this->getServer());
        $this->assertPropertyNull('pdo', $this->getServer());
    }

    public function testGetVersion(): void
    {
        self::assertSame('9.5', $this->getServer()->getVersion());
    }

    public function testPdo(): void
    {
        $pdo = $this->getServer()->pdo();

        $this->assertPropertyInstanceOf(\PDO::class, 'pdo', $this->getServer());
        self::assertFalse($pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES));
    }
}
