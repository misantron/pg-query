<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Exception\ServerException;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Expression\OnConflict;
use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query\Insert;
use Misantron\QueryBuilder\Query\Select;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;

class InsertTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $query = $this->createQuery();

        $this->assertPropertyInstanceOf(Server::class, 'server', $query);
        $this->assertPropertySame('foo.bar', 'table', $query);

        $this->assertPropertySame([], 'columns', $query);
        $this->assertPropertyNull('values', $query);
        $this->assertPropertyNull('rowSet', $query);
    }

    public function testColumnsWithEmptyList(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Column list is empty');

        $query = $this->createQuery();
        $query->columns([]);
    }

    public function testColumns(): void
    {
        $query = $this->createQuery();

        $columnsList = ['foo', 'bar'];
        $query->columns($columnsList);

        $this->assertPropertySame($columnsList, 'columns', $query);

        $columns = 'foo ,  bar ';
        $query->columns($columns);

        $this->assertPropertySame($columnsList, 'columns', $query);
    }

    public function testValuesWithEmptyList(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        $query = $this->createQuery();
        $query->values([]);
    }

    public function testValuesWithSingleRow(): void
    {
        $values = ['foo' => 1, 'bar' => 2];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertPropertySame(['foo', 'bar'], 'columns', $query);
        $this->assertPropertySame([[1, 2]], 'values', $query);
    }

    public function testValuesWithMultipleRows(): void
    {
        $values = [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 3, 'bar' => 4],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertPropertySame(['foo', 'bar'], 'columns', $query);
        $this->assertPropertySame([[1, 2], [3, 4]], 'values', $query);
    }

    public function testOnConflictWithNotAcceptableServerVersion(): void
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Feature available since 9.5 version');

        $server = new Server([], [], '9.2');

        $target = ConflictTarget::fromField('foo');
        $factory = Factory::create($this->createServerMock());
        $factory->setServer($server);

        $action = $factory
            ->update()
            ->set(['foo' => 'bar'])
            ->andEquals('baz', 5);

        $query = $this->createQuery($server);
        $query->onConflict($target, $action);
    }

    public function testOnConflict(): void
    {
        $target = ConflictTarget::fromConstraint('foo_unique');
        $factory = Factory::create($this->createServerMock());

        $action = $factory
            ->update()
            ->set(['foo' => 'bar'])
            ->andEquals('baz', 5);

        $query = $this->createQuery();
        $query->onConflict($target, $action);

        $this->assertPropertyInstanceOf(OnConflict::class, 'onConflict', $query);
    }

    public function testFromRows(): void
    {
        $server = $this->createServerMock();
        $table = 'foo.test';

        $selectQuery = new Select($server, $table);

        $query = $this->createQuery();
        $query->fromRows($selectQuery);

        $this->assertPropertyInstanceOf(Select::class, 'rowSet', $query);
    }

    public function testCompileWithoutColumns(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Column list is empty');

        $query = $this->createQuery();
        $query->compile();
    }

    public function testCompileWithoutValues(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value list is empty');

        $query = $this->createQuery();
        $query->columns(['foo', 'bar']);
        $query->compile();
    }

    public function testCompileWithValues(): void
    {
        $values = [
            ['foo' => 1, 'bar' => 'test1'],
            ['foo' => 3, 'bar' => false],
            ['foo' => 4, 'bar' => null],
            ['foo' => 5, 'bar' => [5, 8]],
        ];

        $query = $this->createQuery();
        $query->values($values);

        $this->assertSame(
            "INSERT INTO foo.bar (foo,bar) VALUES (1,'test1'),(3,false),(4,null),(5,'{5,8}')",
            $query->compile()
        );
    }

    public function testCompileWithOnConflict(): void
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

        $this->assertSame(
            "INSERT INTO foo.bar (foo) VALUES ('bar') " .
            "ON CONFLICT ON CONSTRAINT pk_unique DO UPDATE SET foo = 'bar' WHERE baz = 5",
            $query->compile()
        );
    }

    public function testCompileWithRowSet(): void
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

        $this->assertSame(
            'INSERT INTO bar.foo (foo,bar) SELECT foo,bar FROM foo.bar t1 WHERE test = 1 LIMIT 50 OFFSET 0',
            $query->compile()
        );
    }

    public function testGetInsertedRowWithoutReturningSet(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Returning fields must be set previously');

        $query = $this->createQuery();
        $query
            ->values(['foo' => 1])
            ->getInsertedRow();
    }

    public function testGetInsertedRowBeforeQueryExecute(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query
            ->values(['foo' => 1])
            ->returning('bar')
            ->getInsertedRow();
    }

    public function testGetInsertedRow(): void
    {
        $pdo = $this->createPDOMock();

        $statement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
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

    public function testGetInsertedRowsWithoutReturningSet(): void
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

    public function testGetInsertedRowsBeforeQueryExecute(): void
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

    public function testGetInsertedRows(): void
    {
        $pdo = $this->createPDOMock();

        $statement = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
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

    private function createQuery(?Server $server = null): Insert
    {
        if ($server === null) {
            $server = $this->createServerMock();
        }

        $table = 'foo.bar';

        return new Insert($server, $table);
    }
}
