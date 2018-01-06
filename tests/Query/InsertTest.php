<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Query\Insert;
use MediaTech\Query\Query\Select;
use MediaTech\Query\Tests\BaseTestCase;

class InsertTest extends BaseTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);

        $this->assertAttributeEquals(null, 'columns', $query);
        $this->assertAttributeEquals(null, 'values', $query);
        $this->assertAttributeEquals(null, 'rowSet', $query);
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value list is empty
     */
    public function testValuesWithEmptyList()
    {
        $query = $this->createQuery();
        $query->values([]);
    }

    public function testValuesWithSingleRow()
    {
        $values = ['foo' => 1, 'bar' => 2];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertAttributeEquals(['foo', 'bar'], 'columns', $query);
        $this->assertAttributeEquals([[1, 2]], 'values', $query);
    }

    public function testValuesWithMultipleRows()
    {
        $values = [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 3, 'bar' => 4],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertAttributeEquals(['foo', 'bar'], 'columns', $query);
        $this->assertAttributeEquals([[1, 2], [3, 4]], 'values', $query);
    }

    public function testFromRows()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.test';

        $selectQuery = new Select($pdo, $table);

        $query = $this->createQuery();
        $query->fromRows($selectQuery);

        $this->assertAttributeInstanceOf(Select::class, 'rowSet', $query);
    }

    public function testBuildWithValues()
    {
        $values = [
            ['foo' => 1, 'bar' => 'test1'],
            ['foo' => 3, 'bar' => 'test2'],
        ];

        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['test1', \PDO::PARAM_STR], ['test2', \PDO::PARAM_STR])
            ->willReturnOnConsecutiveCalls("'test1'", "'test2'");

        $query = new Insert($pdo, 'foo.bar');
        $query->values($values);

        $this->assertEquals("INSERT INTO foo.bar (foo,bar) VALUES (1,'test1'),(3,'test2') RETURNING *", $query->build());
    }

    public function testBuildWithRowSet()
    {
        $this->markTestIncomplete('Not ready');
    }

    private function createQuery()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        return new Insert($pdo, $table);
    }
}