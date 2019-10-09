<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Exception\IdentifierException;
use Misantron\QueryBuilder\Expression\Field;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class FieldTest extends UnitTestCase
{
    public function testConstructorWithAliasWithInvalidChars(): void
    {
        $this->expectException(IdentifierException::class);
        $this->expectExceptionMessage('Identifier supplied invalid characters');

        Field::create('SUM(field)', '$f');
    }

    public function testConstructorWithAliasBeginsFromInvalidChar(): void
    {
        $this->expectException(IdentifierException::class);
        $this->expectExceptionMessage('Identifier must begin with a letter or underscore');

        Field::create('SUM(field)', '5f');
    }

    public function testConstructor(): void
    {
        $field = Field::create('SUM(field)');

        $this->assertPropertySame('SUM(field)', 'expression', $field);
        $this->assertPropertySame('', 'alias', $field);

        $field = Field::create('SUM(field)', 'total');

        $this->assertPropertySame('SUM(field)', 'expression', $field);
        $this->assertPropertySame('total', 'alias', $field);
    }

    public function testCompile(): void
    {
        $field = Field::create('SUM(field)');
        $this->assertSame('SUM(field)', $field->compile());

        $field = Field::create('SUM(field)', 'total');
        $this->assertSame('SUM(field) AS total', $field->compile());
    }
}
