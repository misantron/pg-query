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
        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $factory);
    }
}