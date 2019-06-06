<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Server;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase.
 */
abstract class UnitTestCase extends TestCase
{
    /**
     * @return MockObject|PDO
     */
    protected function createPDOMock(): MockObject
    {
        return $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param PDO|null $pdo
     * @return MockObject|Server
     */
    protected function createServerMock(?PDO $pdo = null): MockObject
    {
        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($pdo === null) {
            $pdo = $this->createPDOMock();
        }

        $server
            ->method('pdo')
            ->willReturn($pdo);

        return $server;
    }

    /**
     * @return MockObject|PDOStatement
     */
    protected function createStatementMock()
    {
        return $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
