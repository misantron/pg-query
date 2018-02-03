<?php

namespace MediaTech\Query\Tests\Unit\Expression;


use MediaTech\Query\Expression\Field;
use MediaTech\Query\Tests\BaseTestCase;

class FieldTest extends BaseTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid identifier: invalid characters supplied
     */
    public function testConstructorWithAliasWithInvalidChars()
    {
        Field::create('SUM(field)', '$f');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid identifier: must begin with a letter or underscore
     */
    public function testConstructorWithAliasBeginsFromInvalidChar()
    {
        Field::create('SUM(field)', '5f');
    }

    public function testConstructor()
    {
        $field = Field::create('SUM(field)');

        $this->assertAttributeEquals('SUM(field)', 'expression', $field);
        $this->assertAttributeEquals('', 'alias', $field);

        $field = Field::create('SUM(field)', 'total');

        $this->assertAttributeEquals('SUM(field)', 'expression', $field);
        $this->assertAttributeEquals('total', 'alias', $field);
    }

    public function testBuild()
    {
        $field = Field::create('SUM(field)');
        $this->assertEquals('SUM(field)', $field->__toString());

        $field = Field::create('SUM(field)', 'total');
        $this->assertEquals('SUM(field) AS total', $field->__toString());
    }
}