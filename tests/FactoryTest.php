<?php

namespace MediaTech\Query\Tests;


use MediaTech\Query\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testConstructor()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\PDO $pdo */
        $pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Factory($pdo);
        $this->assertInstanceOf(\PDO::class, 'pdo', $factory);

        $pdo = $factory->getPDO();
        $this->assertEquals(false, \PDO::ATTR_EMULATE_PREPARES, $pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES));
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_ERRMODE, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
    }
}