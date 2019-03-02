<?php

namespace Misantron\QueryBuilder\Tests\Integration;

use Misantron\QueryBuilder\Configuration;
use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Server;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTestCase.
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * @var \PDO
     */
    private $server;

    /**
     * @var Factory
     */
    private $factory;

    protected function setUp(): void
    {
        $configuration = Configuration::createFromDsn(
            'pgsql:host=localhost;port=5432;dbname=test;user=postres;password=1',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
            '9.2'
        );

        $server = new Server($configuration);

        $this->factory = Factory::create($server);
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