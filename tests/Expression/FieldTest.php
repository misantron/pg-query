<?php

namespace MediaTech\Query\Tests\Expression;


use MediaTech\Query\Expression\Field;
use MediaTech\Query\Tests\BaseTestCase;

class FieldTest extends BaseTestCase
{
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