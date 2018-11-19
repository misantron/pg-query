<?php

namespace Misantron\QueryBuilder\Tests\Integration;

use Misantron\QueryBuilder\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTestCase
 * @package Misantron\QueryBuilder\Tests\Integration
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Factory
     */
    private $factory;

    protected function setUp()
    {
        $this->pdo = new \PDO(
            'pgsql:host=localhost;port=5432;dbname=test', 'postgres', '1',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $this->factory = Factory::create($this->pdo);
    }

    /**
     * @return \PDO
     */
    protected function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @return Factory
     */
    protected function getFactory(): Factory
    {
        return $this->factory;
    }
}