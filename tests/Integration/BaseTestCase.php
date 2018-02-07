<?php

namespace MediaTech\Query\Tests\Integration;


use MediaTech\Query\Factory;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
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