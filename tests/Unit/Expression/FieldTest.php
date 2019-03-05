<?php

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Expression\Field;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class FieldTest extends UnitTestCase
{
    public function testConstructorWithAliasWithInvalidChars()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid identifier: invalid characters supplied');

        Field::create('SUM(field)', '$f');
    }

    public function testConstructorWithAliasBeginsFromInvalidChar()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid identifier: must begin with a letter or underscore');

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
        $this->assertSame('SUM(field)', $field->__toString());

        $field = Field::create('SUM(field)', 'total');
        $this->assertSame('SUM(field) AS total', $field->__toString());
    }
}
