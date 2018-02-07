<?php

namespace MediaTech\Query\Tests\Unit;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @return MockObject|\PDO
     */
    protected function createPDOMock()
    {
        $pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $pdo;
    }

    /**
     * @return MockObject|\PDOStatement
     */
    protected function createStatementMock()
    {
        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $statement;
    }
}