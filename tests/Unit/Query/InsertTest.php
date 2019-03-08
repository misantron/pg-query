<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query\Insert;
use Misantron\QueryBuilder\Query\Select;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class InsertTest extends UnitTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(Server::class, 'server', $query);
        $this->assertAttributeSame('foo.bar', 'table', $query);

        $this->assertAttributeSame([], 'columns', $query);
        $this->assertAttributeSame(null, 'values', $query);
        $this->assertAttributeSame(null, 'rowSet', $query);
    }

    public function testColumnsWithEmptyList()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Column list is empty');

        $query = $this->createQuery();
        $query->columns([]);
    }

    public function testColumns()
    {
        $query = $this->createQuery();

        $columnsList = ['foo', 'bar'];
        $query->columns($columnsList);

        $this->assertAttributeSame($columnsList, 'columns', $query);

        $columns = 'foo ,  bar ';
        $query->columns($columns);

        $this->assertAttributeSame($columnsList, 'columns', $query);
    }

    public function testValuesWithEmptyList()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value list is empty');

        $query = $this->createQuery();
        $query->values([]);
    }

    public function testValuesWithSingleRow()
    {
        $values = ['foo' => 1, 'bar' => 2];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertAttributeSame(['foo', 'bar'], 'columns', $query);
        $this->assertAttributeSame([[1, 2]], 'values', $query);
    }

    public function testValuesWithMultipleRows()
    {
        $values = [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 3, 'bar' => 4],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertAttributeSame(['foo', 'bar'], 'columns', $query);
        $this->assertAttributeSame([[1, 2], [3, 4]], 'values', $query);
    }

    public function testOnConflictWithoutAction()
    {
        $target = ConflictTarget::fromField('foo');

        $query = $this->createQuery();
        $query->onConflict($target);

        $this->assertAttributeInstanceOf(ConflictTarget::class, 'conflictTarget', $query);
        $this->assertAttributeSame(null, 'conflictAction', $query);
    }

    public function testOnConflictWithTargetAndAction()
    {
        $target = ConflictTarget::fromConstraint('foo_unique');
        $factory = Factory::create($this->createServerMock());

        /** @var Update $action */
        $action = $factory
            ->update()
            ->set(['foo' => 'bar'])
            ->andEquals('baz', 5);

        $query = $this->createQuery();
        $query->onConflict($target, $action);

        $this->assertAttributeInstanceOf(ConflictTarget::class, 'conflictTarget', $query);
        $this->assertAttributeInstanceOf(Update::class, 'conflictAction', $query);
    }

    public function testFromRows()
    {
        $server = $this->createServerMock();
        $table = 'foo.test';

        $selectQuery = new Select($server, $table);

        $query = $this->createQuery();
        $query->fromRows($selectQuery);

        $this->assertAttributeInstanceOf(Select::class, 'rowSet', $query);
    }

    public function testBuildWithoutColumns()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Column list is empty');

        $query = $this->createQuery();
        $query->__toString();
    }

    public function testBuildWithoutValues()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value list is empty');

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
            ['foo' => 5, 'bar' => [5, 8]],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertSame("INSERT INTO foo.bar (foo,bar) VALUES (1,'test1'),(3,false),(4,null),(5,ARRAY[5,8]::INTEGER[])", $query->__toString());
    }

    public function testBuildWithOnConflict()
    {
        $target = ConflictTarget::fromConstraint('pk_unique');
        $factory = Factory::create($this->createServerMock());

        /** @var Update $action */
        $action = $factory
            ->update()
            ->set(['foo' => 'bar'])
            ->andEquals('baz', 5);

        $query = $this->createQuery();
        $query
            ->values(['foo' => 'bar'])
            ->onConflict($target, $action);

        $this->assertSame("INSERT INTO foo.bar (foo) VALUES ('bar') ON CONFLICT ON CONSTRAINT pk_unique DO UPDATE SET foo = 'bar' WHERE baz = 5", $query->__toString());
    }

    public function testBuildWithRowSet()
    {
        $server = $this->createServerMock();
        $columns = ['foo', 'bar'];

        $rowSetQuery = Factory::create($server)
            ->select('foo.bar');

        $rowSetQuery
            ->columns($columns)
            ->range(0, 50)
            ->andEquals('test', 1);

        $query = new Insert($server, 'bar.foo');
        $query
            ->columns($columns)
            ->fromRows($rowSetQuery);

        $this->assertSame('INSERT INTO bar.foo (foo,bar) SELECT foo,bar FROM foo.bar t1 WHERE test = 1 LIMIT 50 OFFSET 0', $query->__toString());
    }

    public function testToString()
    {
        $query = $this->createQuery();
        $query->values(['foo' => 1]);

        $this->assertSame((string)$query, $query->__toString());
    }

    public function testGetInsertedRowWithoutReturningSet()
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Returning fields must be set previously');

        $query = $this->createQuery();
        $query
            ->values(['foo' => 1])
            ->getInsertedRow();
    }

    public function testGetInsertedRowBeforeQueryExecute()
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query
            ->values(['foo' => 1])
            ->returning('bar')
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
                'foo' => 'bar',
            ]);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO foo.bar (foo) VALUES ('bar') RETURNING foo")
            ->willReturn($statement);

        /** @var MockObject|Server $server */
        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        $server
            ->method('pdo')
            ->willReturn($pdo);

        $query = new Insert($server, 'foo.bar');

        $inserted = $query
            ->values([
                'foo' => 'bar',
            ])
            ->returning('foo')
            ->execute()
            ->getInsertedRow();

        $this->assertSame([
            'id' => 1,
            'foo' => 'bar',
        ], $inserted);
    }

    public function testGetInsertedRowsWithoutReturningSet()
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Returning fields must be set previously');

        $query = $this->createQuery();
        $query
            ->values([
                ['foo' => 1],
                ['foo' => 2],
            ])
            ->getInsertedRows();
    }

    public function testGetInsertedRowsBeforeQueryExecute()
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query
            ->values([
                ['foo' => 1],
                ['foo' => 2],
            ])
            ->returning('bar')
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
                    'foo' => 'bar',
                ],
                [
                    'id' => 2,
                    'foo' => 'baz',
                ],
            ]);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO foo.bar (foo) VALUES ('bar'),('baz') RETURNING id")
            ->willReturn($statement);

        /** @var MockObject|Server $server */
        $server = $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();

        $server
            ->method('pdo')
            ->willReturn($pdo);

        $query = new Insert($server, 'foo.bar');

        $inserted = $query
            ->values([
                ['foo' => 'bar'],
                ['foo' => 'baz'],
            ])
            ->returning('id')
            ->execute()
            ->getInsertedRows();

        $this->assertSame([
            [
                'id' => 1,
                'foo' => 'bar',
            ],
            [
                'id' => 2,
                'foo' => 'baz',
            ],
        ], $inserted);
    }

    private function createQuery(): Insert
    {
        $server = $this->createServerMock();
        $table = 'foo.bar';

        return new Insert($server, $table);
    }
}
