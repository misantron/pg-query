<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Server;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase.
 */
abstract class UnitTestCase extends TestCase
{
    use AssertObjectProperty;

    /**
     * @return MockObject|\PDO
     */
    protected function createPDOMock(): MockObject
    {
        return $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepare'])
            ->getMock();
    }

    /**
     * @param \PDO|null $pdo
     * @return MockObject|Server
     */
    protected function createServerMock(?\PDO $pdo = null): MockObject
    {
        $server = $this->createMock(Server::class);

        if ($pdo === null) {
            $pdo = $this->createPDOMock();
        }

        $server
            ->method('pdo')
            ->willReturn($pdo);

        return $server;
    }

    /**
     * @return MockObject|\PDOStatement
     */
    protected function createStatementMock(): MockObject
    {
        $mock = $this->createMock(\PDOStatement::class);
        $mock
            ->method('execute')
            ->willReturn(true);

        return $mock;
    }
}
