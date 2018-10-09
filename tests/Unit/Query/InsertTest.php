<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query\Insert;
use Misantron\QueryBuilder\Query\Select;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class InsertTest extends UnitTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Table name is empty
     */
    public function testConstructorWithEmptyTable()
    {
        $pdo = $this->createPDOMock();

        new Insert($pdo, '');
    }

    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);

        $this->assertAttributeEquals([], 'columns', $query);
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Column list is empty
     */
    public function testBuildWithoutColumns()
    {
        $query = $this->createQuery();
        $query->__toString();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Value list is empty
     */
    public function testBuildWithoutValues()
    {
        $query = $this->createQuery();
        $query->columns(['foo', 'bar']);
        $query->__toString();
    }

    public function testBuildWithValues()
    {
        $values = [
            ['foo' => 1, 'bar' => 'test1'],
            ['foo' => 3, 'bar' => false],
            ['foo' => 4, 'bar' => null],
            ['foo' => 5, 'bar' => [5,8]],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertEquals("INSERT INTO foo.bar (foo,bar) VALUES (1,'test1'),(3,false),(4,null),(5,ARRAY[5,8]::INTEGER[]) RETURNING *", $query->__toString());
    }

    public function testBuildWithRowSet()
    {
        $pdo = $this->createPDOMock();
        $columns = ['foo', 'bar'];

        $rowSetQuery = Factory::create($pdo)->select('foo.bar');

        $rowSetQuery
            ->columns($columns)
            ->range(0, 50)
            ->andEquals('test', 1);

        $query = new Insert($pdo, 'bar.foo');
        $query
            ->columns($columns)
            ->fromRows($rowSetQuery);

        $this->assertEquals('INSERT INTO bar.foo (foo,bar) SELECT foo,bar FROM foo.bar t1 WHERE test = 1 LIMIT 50 OFFSET 0', $query->__toString());
    }

    public function testToString()
    {
        $query = $this->createQuery();
        $query->values(['foo' => 1]);

        $this->assertEquals((string)$query, $query->__toString());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data fetch error: query must be executed before fetch data
     */
    public function testGetInsertedRowBeforeQueryExecute()
    {
        $query = $this->createQuery();
        $query
            ->values(['foo' => 1])
            ->getInsertedRow();
    }

    public function testGetInsertedRow()
    {
        $pdo = $this->createPDOMock();

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => 1,
                'foo' => 'bar'
            ]);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO foo.bar (foo) VALUES ('bar') RETURNING *")
            ->willReturn($statement);

        $query = new Insert($pdo, 'foo.bar');

        $inserted = $query
            ->values([
                'foo' => 'bar'
            ])
            ->execute()
            ->getInsertedRow();

        $this->assertEquals([
            'id' => 1,
            'foo' => 'bar'
        ], $inserted);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data fetch error: query must be executed before fetch data
     */
    public function testGetInsertedRowsBeforeQueryExecute()
    {
        $query = $this->createQuery();
        $query
            ->values([
                ['foo' => 1],
                ['foo' => 2],
            ])
            ->getInsertedRows();
    }

    public function testGetInsertedRows()
    {
        $pdo = $this->createPDOMock();

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'foo' => 'bar'
                ],
                [
                    'id' => 2,
                    'foo' => 'baz'
                ]
            ]);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO foo.bar (foo) VALUES ('bar'),('baz') RETURNING *")
            ->willReturn($statement);

        $query = new Insert($pdo, 'foo.bar');

        $inserted = $query
            ->values([
                ['foo' => 'bar'],
                ['foo' => 'baz'],
            ])
            ->execute()
            ->getInsertedRows();

        $this->assertEquals([
            [
                'id' => 1,
                'foo' => 'bar'
            ],
            [
                'id' => 2,
                'foo' => 'baz'
            ]
        ], $inserted);
    }

    private function createQuery()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        return new Insert($pdo, $table);
    }
}