<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Server;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase.
 */
abstract class UnitTestCase extends TestCase
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
     * @param \PDO|null $pdo
     * @return MockObject|Server
     */
    protected function createServerMock(?\PDO $pdo = null)
    {
        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdo = $pdo ?: $this->createPDOMock();

        $server
            ->method('pdo')
            ->willReturn($pdo);
        $server
            ->method('version')
            ->willReturn('9.5');

        return $server;
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
