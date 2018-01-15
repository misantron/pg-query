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

    public function testAlias()
    {
        $query = $this->createQuery();
        $query->alias('s1');

        $this->assertAttributeEquals('s1', 'alias', $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Column list is empty
     */
    public function testColumnsWithEmptyList()
    {
        $query = $this->createQuery();
        $query->columns([]);
    }

    public function testColumns()
    {
        $query = $this->createQuery();

        $columnsList = ['foo', 'bar'];
        $query->columns($columnsList);

        $this->assertAttributeEquals($columnsList, 'columns', $query);

        $columns = 'foo ,  bar ';
        $query->columns($columns);

        $this->assertAttributeEquals($columnsList, 'columns', $query);
    }

    public function testDistinct()
    {
        $query = $this->createQuery();
        $query->distinct();

        $this->assertAttributeEquals(true, 'distinct', $query);

        $query->distinct(false);

        $this->assertAttributeEquals(false, 'distinct', $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid fetch mode
     */
    public function testFetchModeWithInvalidValue()
    {
        $query = $this->createQuery();
        $query->fetchMode(100500);
    }

    public function testFetchMode()
    {
        $query = $this->createQuery();
        $query->fetchMode(Select\FetchMode::COLUMN);

        $this->assertAttributeEquals(Select\FetchMode::COLUMN, 'fetchMode', $query);
    }

    private function createQuery()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        return new Select($pdo, $table);
    }
}