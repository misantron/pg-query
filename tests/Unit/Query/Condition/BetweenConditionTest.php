<?php

namespace MediaTech\Query\Tests\Unit\Query\Condition;


use MediaTech\Query\Helper\Escape;
use MediaTech\Query\Query\Condition\BetweenCondition;
use MediaTech\Query\Tests\Unit\BaseTestCase;

class BetweenConditionTest extends BaseTestCase
{
    use Escape;

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value: array must contains two elements - begin and end of period
     */
    public function testConstructorWithInvalidPeriod()
    {
        new BetweenCondition('foo', [(new \DateTime())->format('Y-m-d H:i:s')]);
    }

    public function testConstructor()
    {
        $values = [
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->sub(new \DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = new BetweenCondition('foo', $values);

        $expected = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($expected, 'values', $condition);
    }

    public function testCreate()
    {
        $values = [
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->sub(new \DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = BetweenCondition::create('foo', $values);

        $expected = array_map(function ($value) {
            return $this->escapeValue($value);
        }, $values);

        $this->assertAttributeEquals('foo', 'column', $condition);
        $this->assertAttributeEquals($expected, 'values', $condition);
    }

    public function testToString()
    {
        $values = [
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->sub(new \DateInterval('P1D'))->format('Y-m-d H:i:s'),
        ];

        $condition = BetweenCondition::create('foo', $values);

        list($begin, $end) = $values;

        $this->assertEquals(sprintf("foo BETWEEN '%s' AND '%s'", $begin, $end), $condition->__toString());
        $this->assertEquals(sprintf("foo BETWEEN '%s' AND '%s'", $begin, $end), (string)$condition);
    }
}