<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Integration;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\AssertObjectProperty;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTestCase.
 */
abstract class IntegrationTestCase extends TestCase
{
    use AssertObjectProperty;

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
                'host' => getenv('POSTGRES_HOST'),
                'port' => '5432',
                'dbname' => 'postgres',
                'user' => 'postgres',
                'password' => 'postgres',
            ],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
            '9.5'
        );

        $this->factory = Factory::create($this->server);
    }

    protected function getServer(): Server
    {
        return $this->server;
    }

    protected function getFactory(): Factory
    {
        return $this->factory;
    }
}
