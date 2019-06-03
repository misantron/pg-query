<?php

namespace Misantron\QueryBuilder\Tests\Unit\Query;

use Misantron\QueryBuilder\Exception\QueryLogicException;
use Misantron\QueryBuilder\Exception\QueryParameterException;
use Misantron\QueryBuilder\Exception\QueryRuntimeException;
use Misantron\QueryBuilder\Expression\Field;
use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Query\Insert;
use Misantron\QueryBuilder\Query\Select;
use Misantron\QueryBuilder\Server;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;
use PDO;
use PDOStatement;
use stdClass;

class SelectTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $server = $this->createServerMock();
        $table = 'foo.bar';

        $query = new Select($server, $table);

        $this->assertAttributeInstanceOf(Server::class, 'server', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeSame('foo.bar', 'table', $query);
        $this->assertAttributeSame('t1', 'alias', $query);
        $this->assertAttributeSame([], 'columns', $query);
        $this->assertAttributeSame([], 'joins', $query);
        $this->assertAttributeSame([], 'groupBy', $query);
        $this->assertAttributeSame([], 'orderBy', $query);
        $this->assertAttributeSame([], 'with', $query);
        $this->assertAttributeSame(null, 'distinct', $query);
        $this->assertAttributeSame(null, 'having', $query);
        $this->assertAttributeSame(null, 'limit', $query);
        $this->assertAttributeSame(null, 'offset', $query);

        $query = new Select($server, $table, 'test');

        $this->assertAttributeSame('test', 'alias', $query);
    }

    public function testAlias(): void
    {
        $query = $this->createQuery();
        $query->alias('s1');

        $this->assertAttributeSame('s1', 'alias', $query);
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

        $this->assertAttributeSame($columnsList, 'columns', $query);

        $columns = 'foo ,  bar ';
        $query->columns($columns);

        $this->assertAttributeSame($columnsList, 'columns', $query);
    }

    public function testDistinct(): void
    {
        $query = $this->createQuery();
        $query->distinct();

        $this->assertAttributeSame(true, 'distinct', $query);

        $query->distinct(false);

        $this->assertAttributeSame(false, 'distinct', $query);
    }

    public function testJoinWithAlreadyJoinedTable(): void
    {
        $this->expectException(QueryLogicException::class);
        $this->expectExceptionMessage('Table has already joined with same alias');

        $query = $this->createQuery();
        $query->innerJoin('test', 't2', 't2.id = t1.user_id');
        $query->innerJoin('test', 't2', 't2.id = t1.user_type');
    }

    public function testJoinWithDuplicatedAlias(): void
    {
        $this->expectException(QueryLogicException::class);
        $this->expectExceptionMessage('Table alias is already in use');

        $query = $this->createQuery();
        $query->innerJoin('test', 't2', 't2.id = t1.user_id');
        $query->innerJoin('any', 't2', 't2.id = t1.user_type');
    }

    public function testInnerJoin(): void
    {
        $query = $this->createQuery();
        $query->innerJoin('test', 't2', 't2.id = t1.user_id');

        $hash = sha1('inner_test_t2');

        $this->assertAttributeSame([
            $hash => [
                'type' => 'inner',
                'table' => 'test',
                'alias' => 't2',
                'condition' => 't2.id = t1.user_id',
            ],
        ], 'joins', $query);
    }

    public function testLeftJoin(): void
    {
        $query = $this->createQuery();
        $query->leftJoin('test', 't2', 't2.id = t1.user_id');

        $hash = sha1('left_test_t2');

        $this->assertAttributeSame([
            $hash => [
                'type' => 'left',
                'table' => 'test',
                'alias' => 't2',
                'condition' => 't2.id = t1.user_id',
            ],
        ], 'joins', $query);
    }

    public function testWithWithInvalidQuery(): void
    {
        $this->expectException(QueryParameterException::class);
        $this->expectExceptionMessage('Value must be a select query instance');

        $factory = Factory::create($this->createServerMock());

        $insertQuery = $this->getMockBuilder(Insert::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => $insertQuery,
        ];

        $query = $this->createQuery();
        $query->with($cte);
    }

    public function testWith(): void
    {
        $factory = Factory::create($this->createServerMock());

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => $factory
                ->select('regional_sales')
                ->columns(['region'])
                ->andMore('total_sales', 1000),
        ];

        $query = $this->createQuery();
        $query->with($cte);

        $this->assertAttributeSame($cte, 'with', $query);
    }

    public function testGroupBy(): void
    {
        $query = $this->createQuery();
        $query->groupBy(['test']);

        $this->assertAttributeSame(['test'], 'groupBy', $query);
    }

    public function testOrderBy(): void
    {
        $query = $this->createQuery();
        $query->orderBy(['test', 'field desc']);

        $this->assertAttributeSame(['test', 'field desc'], 'orderBy', $query);
    }

    public function testHaving(): void
    {
        $query = $this->createQuery();
        $query->having('total_amount >= 1500');

        $this->assertAttributeSame('total_amount >= 1500', 'having', $query);
    }

    public function testLimit(): void
    {
        $query = $this->createQuery();
        $query->limit(500);

        $this->assertAttributeSame(500, 'limit', $query);
    }

    public function testOffset(): void
    {
        $query = $this->createQuery();
        $query->offset(20);

        $this->assertAttributeSame(20, 'offset', $query);
    }

    public function testRange(): void
    {
        $query = $this->createQuery();
        $query->range(100, 500);

        $this->assertAttributeSame(100, 'offset', $query);
        $this->assertAttributeSame(500, 'limit', $query);
    }

    public function testCompileWithHavingWithoutGroupBy(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Using having without group by');

        $query = $this->createQuery();
        $query
            ->having('total_amount >= 1500')
            ->compile();
    }

    public function testCompile(): void
    {
        $factory = Factory::create($this->createServerMock());

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => $factory
                ->select('regional_sales')
                ->columns(['region'])
                ->andMore('total_sales', 1000),
        ];

        $query = $factory
            ->select('foo.bar')
            ->with($cte)
            ->columns(['field1', 'field2'])
            ->innerJoin('test', 't2', 't2.id = t1.user_id')
            ->andIn('field1', [3, 7, 9])
            ->orIsNull('field2')
            ->having('total_amount >= 1500')
            ->groupBy('region')
            ->orderBy(['region desc'])
            ->limit(1000)
            ->offset(150);

        $this->assertSame(
            'WITH regional_sales AS (SELECT region,SUM(amount) AS total_sales ' .
            'FROM orders t1 GROUP BY region), top_regions AS (SELECT region FROM regional_sales t1 ' .
            'WHERE total_sales > 1000) SELECT field1,field2 FROM foo.bar t1 ' .
            'INNER JOIN test t2 ON t2.id = t1.user_id WHERE field1 IN (3,7,9) OR field2 IS NULL ' .
            'GROUP BY region HAVING total_amount >= 1500 ORDER BY region desc LIMIT 1000 OFFSET 150',
            $query->compile()
        );
    }

    public function testBuildConditionGroups(): void
    {
        $query = $this->createQuery();

        $query
            ->beginGroup()
            ->equals('foo', 1)
            ->orIsNull('bar')
            ->endGroup();

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE ( foo = 1 OR bar IS NULL )', $query->compile());

        $query = $this->createQuery();

        $query
            ->equals('test', 5)
            ->orGroup()
            ->equals('foo', 1)
            ->orIsNull('bar')
            ->endGroup();

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE test = 5 OR ( foo = 1 OR bar IS NULL )', $query->compile());
    }

    public function testBuildConditionsWithEqualFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->equals('foo', 1)
            ->andEquals('bar', false)
            ->orEquals('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo = 1 AND bar = false OR baz = 10', $query->compile());
    }

    public function testBuildConditionsWithNotEqualFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->notEquals('foo', 1)
            ->andNotEquals('bar', false)
            ->orNotEquals('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo != 1 AND bar != false OR baz != 10', $query->compile());
    }

    public function testBuildConditionsWithMoreFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->more('foo', 1)
            ->andMore('bar', 5)
            ->orMore('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo > 1 AND bar > 5 OR baz > 10', $query->compile());
    }

    public function testBuildConditionsWithMoreOrEqualsFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->moreOrEquals('foo', 1)
            ->andMoreOrEquals('bar', 5)
            ->orMoreOrEquals('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo >= 1 AND bar >= 5 OR baz >= 10', $query->compile());
    }

    public function testBuildConditionsWithLessFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->less('foo', 1)
            ->andLess('bar', 5)
            ->orLess('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo < 1 AND bar < 5 OR baz < 10', $query->compile());
    }

    public function testBuildConditionsWithLessOrEqualsFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->lessOrEquals('foo', 1)
            ->andLessOrEquals('bar', 5)
            ->orLessOrEquals('baz', 10);

        $this->assertSame('SELECT * FROM foo.bar t1 WHERE foo <= 1 AND bar <= 5 OR baz <= 10', $query->compile());
    }

    public function testBuildConditionsWithRangeFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->between('foo', [1, 2])
            ->andBetween('bar', [5, 6])
            ->orBetween('baz', [10, 20]);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE foo BETWEEN 1 AND 2 AND bar BETWEEN 5 AND 6 OR baz BETWEEN 10 AND 20',
            $query->compile()
        );
    }

    public function testBuildConditionsWithNullFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->isNull('foo')
            ->andIsNull('bar')
            ->orIsNull('baz');

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE foo IS NULL AND bar IS NULL OR baz IS NULL',
            $query->compile()
        );
    }

    public function testBuildConditionsWithNotNullFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->isNotNull('foo')
            ->andIsNotNull('bar')
            ->orIsNotNull('baz');

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE foo IS NOT NULL AND bar IS NOT NULL OR baz IS NOT NULL',
            $query->compile()
        );
    }

    public function testBuildConditionsWithInFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->in('foo', [1, 2])
            ->andIn('bar', [5, 6])
            ->orIn('baz', [10, 20]);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE foo IN (1,2) AND bar IN (5,6) OR baz IN (10,20)',
            $query->compile()
        );
    }

    public function testBuildConditionsWithNotInFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->notIn('foo', [1, 2])
            ->andNotIn('bar', [5, 6])
            ->orNotIn('baz', [10, 20]);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE foo NOT IN (1,2) AND bar NOT IN (5,6) OR baz NOT IN (10,20)',
            $query->compile()
        );
    }

    public function testBuildConditionsWithInArrayFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->inArray('foo', 1)
            ->andInArray('bar', 5)
            ->orInArray('baz', 10);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE 1 = ANY(foo) AND 5 = ANY(bar) OR 10 = ANY(baz)',
            $query->compile()
        );
    }

    public function testBuildConditionsWithNotInArrayFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->notInArray('foo', 1)
            ->andNotInArray('bar', 5)
            ->orNotInArray('baz', 10);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 WHERE 1 != ANY(foo) AND 5 != ANY(bar) OR 10 != ANY(baz)',
            $query->compile()
        );
    }

    public function testBuildConditionsWithArrayContainsFilters(): void
    {
        $query = $this->createQuery();

        $query
            ->arrayContains('foo', [1, 2])
            ->andArrayContains('bar', [5, 6])
            ->orArrayContains('baz', [10, 20]);

        $this->assertSame(
            'SELECT * FROM foo.bar t1 ' .
            'WHERE foo @> ARRAY[1,2]::INTEGER[] AND bar @> ARRAY[5,6]::INTEGER[] OR baz @> ARRAY[10,20]::INTEGER[]',
            $query->compile()
        );
    }

    public function testExecute(): void
    {
        $pdo = $this->createPDOMock();

        $qs = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';
        $statement = new PDOStatement();

        $pdo
            ->method('prepare')
            ->with($qs)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10);

        $this->assertInstanceOf(Select::class, $query->execute());
        $this->assertAttributeInstanceOf(PDOStatement::class, 'statement', $query);
    }

    public function testRowCountBeforeQueryExecute(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query->rowsCount();
    }

    public function testRowCount(): void
    {
        $pdo = $this->createPDOMock();
        $statement = $this->createStatementMock();

        $qs = 'SELECT * FROM foo.bar t1 LIMIT 10';

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn(5);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($qs)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->limit(10)
            ->execute();

        $this->assertSame(5, $query->rowsCount());
    }

    public function testFetchAllObjectBeforeExecute(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query->fetchAllObject(stdClass::class);
    }

    public function testFetchAllObject(): void
    {
        $pdo = $this->createPDOMock();

        $qs = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            new stdClass(),
            new stdClass(),
        ];

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_CLASS, stdClass::class)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($qs)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertSame($data, $query->fetchAllObject(stdClass::class));
    }

    public function testFetchCallback(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            new stdClass(),
            new stdClass(),
        ];
        $callback = static function () {
            return null;
        };

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_FUNC, $callback)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertSame($data, $query->fetchCallback($callback));
    }

    public function testFetchAllAssoc(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 3, 'bar' => 4],
        ];

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertSame($data, $query->fetchAllAssoc());
    }

    public function testFetchKeyPair(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT field1,field2 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            [1 => 'foo'],
            [2 => 'bar'],
        ];

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_KEY_PAIR)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->columns(['field1', 'field2'])
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertSame($data, $query->fetchKeyValue());
    }

    public function testFetchAllColumn(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT field1 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [1, 2];

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_COLUMN)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->columns('field1')
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertSame($data, $query->fetchAllColumn());
    }

    public function testFetchOneObjectBeforeExecute(): void
    {
        $this->expectException(QueryRuntimeException::class);
        $this->expectExceptionMessage('Query must be executed before data fetching');

        $query = $this->createQuery();
        $query->fetchOneObject(stdClass::class);
    }

    public function testFetchOneObject(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data = new stdClass();

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchObject')
            ->with(stdClass::class)
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertSame($data, $query->fetchOneObject(stdClass::class));
    }

    public function testFetchOneAssoc(): void
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data = ['foo' => 1, 'bar' => 2];

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetch')
            ->willReturn($data);

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertSame($data, $query->fetchOneAssoc());
    }

    public function testFetchColumn(): void
    {
        $query = 'SELECT field1 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data = 1;

        $statement = $this->createStatementMock();

        $statement
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $statement
            ->expects($this->once())
            ->method('fetchColumn')
            ->willReturn($data);

        $pdo = $this->createPDOMock();

        $pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $server = $this->createServerMock($pdo);

        $query = new Select($server, 'foo.bar');

        $query
            ->columns('field1')
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertSame($data, $query->fetchColumn());
    }

    private function createQuery(): Select
    {
        $server = $this->createServerMock();
        $table = 'foo.bar';

        return new Select($server, $table);
    }
}
