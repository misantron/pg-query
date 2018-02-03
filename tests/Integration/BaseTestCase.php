<?php

namespace MediaTech\Query\Tests\Integration;


use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @var \PDO
     */
    private $pdo;

    protected function setUp()
    {
        $this->pdo = new \PDO('pgsql:host=localhost;port=5432;dbname=test', 'postgres', '');
    }

    /**
     * @return \PDO
     */
    protected function getConnection(): \PDO
    {
        return $this->pdo;
    }
}