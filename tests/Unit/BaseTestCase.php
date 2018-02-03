<?php

namespace MediaTech\Query\Tests\Unit;


use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\PDO
     */
    public function createPDOMock()
    {
        $pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $pdo;
    }
}