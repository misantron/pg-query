<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Query\Select;
use MediaTech\Query\Tests\BaseTestCase;

class SelectTest extends BaseTestCase
{
    public function testConstructor()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        $query = new Select($pdo, $table);

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);
        $this->assertAttributeEquals(Select::DEFAULT_TABLE_ALIAS, 'alias', $query);
        $this->assertAttributeEquals(Select\FetchMode::ASSOC, 'fetchMode', $query);
        $this->assertAttributeEquals([], 'columns', $query);
        $this->assertAttributeEquals([], 'joins', $query);
        $this->assertAttributeEquals([], 'groupBy', $query);
        $this->assertAttributeEquals([], 'orderBy', $query);
        $this->assertAttributeEquals([], 'with', $query);
        $this->assertAttributeEquals(null, 'distinct', $query);
        $this->assertAttributeEquals(null, 'having', $query);
        $this->assertAttributeEquals(null, 'limit', $query);
        $this->assertAttributeEquals(null, 'offset', $query);

        $query = new Select($pdo, $table, 'test');

        $this->assertAttributeEquals('test', 'alias', $query);

        $query = new Select($pdo, $table, 'test', Select\FetchMode::OBJECT);

        $this->assertAttributeEquals(Select\FetchMode::OBJECT, 'fetchMode', $query);
    }
}