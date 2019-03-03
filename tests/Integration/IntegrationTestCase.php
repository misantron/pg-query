<?php

namespace Misantron\QueryBuilder\Tests\Integration;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Server;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTestCase.
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var Factory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->server = new Server(
            [
                'host' => 'localhost',
                'port' => '5432',
                'dbname' => 'test',
                'user' => 'postgres',
                'password' => '1',
            ],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
            '9.5'
        );

        $this->factory = Factory::create($this->server);
    }

    /**
     * @return Server
     */
    protected function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return Factory
     */
    protected function getFactory(): Factory
    {
        return $this->factory;
    }
}