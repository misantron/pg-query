<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Integration;

class ServerTest extends IntegrationTestCase
{
    public function testConstructor(): void
    {
        $expectedCredentials = [
            'host' => getenv('POSTGRES_HOST'),
            'port' => '5432',
            'dbname' => getenv('POSTGRES_DATABASE'),
            'user' => getenv('POSTGRES_USER'),
            'password' => getenv('POSTGRES_PASSWORD'),
        ];
        $expectedOptions = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::assertPropertySame($expectedCredentials, 'credentials', $this->getServer());
        self::assertPropertySame($expectedOptions, 'options', $this->getServer());
        self::assertPropertySame(getenv('POSTGRES_VERSION'), 'version', $this->getServer());
        self::assertPropertyNull('pdo', $this->getServer());
    }

    public function testGetVersion(): void
    {
        self::assertSame(getenv('POSTGRES_VERSION'), $this->getServer()->getVersion());
    }

    public function testPdo(): void
    {
        $pdo = $this->getServer()->pdo();

        self::assertPropertyInstanceOf(\PDO::class, 'pdo', $this->getServer());
        self::assertFalse($pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES));
    }
}
