<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Factory;
use MediaTech\Query\Expression\Field;
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
        $this->assertAttributeEquals(\PDO::FETCH_ASSOC, 'fetchMode', $query);
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

        $query = new Select($pdo, $table, 'test', \PDO::FETCH_CLASS);

        $this->assertAttributeEquals(\PDO::FETCH_CLASS, 'fetchMode', $query);
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
        $query->fetchMode(\PDO::FETCH_BOUND);
    }

    public function testFetchMode()
    {
        $query = $this->createQuery();
        $query->fetchMode(\PDO::FETCH_COLUMN);

        $this->assertAttributeEquals(\PDO::FETCH_COLUMN, 'fetchMode', $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Table has already joined
     */
    public function testJoinWithAlreadyJoinedTable()
    {
        $query = $this->createQuery();
        $query->join('test', 't2', 't2.id = t1.user_id');
        $query->join('test', 't2', 't2.id = t1.user_type');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Alias is already in use
     */
    public function testJoinWithDuplicatedAlias()
    {
        $query = $this->createQuery();
        $query->join('test', 't2', 't2.id = t1.user_id');
        $query->join('any', 't2', 't2.id = t1.user_type');
    }

    public function testInnerJoin()
    {
        $query = $this->createQuery();
        $query->innerJoin('test', 't2', 't2.id = t1.user_id');

        $hash = hash('crc32', 'test_t2');

        $this->assertAttributeEquals([
            $hash => [
                'type' => 'inner',
                'table' => 'test',
                'alias' => 't2',
                'condition' => 't2.id = t1.user_id',
            ]
        ], 'joins', $query);
    }

    public function testLeftJoin()
    {
        $query = $this->createQuery();
        $query->leftJoin('test', 't2', 't2.id = t1.user_id');

        $hash = hash('crc32', 'test_t2');

        $this->assertAttributeEquals([
            $hash => [
                'type' => 'left',
                'table' => 'test',
                'alias' => 't2',
                'condition' => 't2.id = t1.user_id',
            ]
        ], 'joins', $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only select query can be used
     */
    public function testWithWithInvalidQuery()
    {
        $factory = Factory::create($this->createPDOMock());

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => new \stdClass()
        ];

        $query = $this->createQuery();
        $query->with($cte);
    }

    public function testWith()
    {
        $factory = Factory::create($this->createPDOMock());

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => $factory
                ->select('regional_sales')
                ->columns(['region'])
                ->andMore('total_sales', 1000)
        ];

        $query = $this->createQuery();
        $query->with($cte);

        $this->assertAttributeEquals($cte, 'with', $query);
    }

    public function testGroupBy()
    {
        $query = $this->createQuery();
        $query->groupBy(['test']);

        $this->assertAttributeEquals(['test'], 'groupBy', $query);
    }

    public function testOrderBy()
    {
        $query = $this->createQuery();
        $query->orderBy(['test', 'field desc']);

        $this->assertAttributeEquals(['test', 'field desc'], 'orderBy', $query);
    }

    public function testHaving()
    {
        $query = $this->createQuery();
        $query->having('total_amount >= 1500');

        $this->assertAttributeEquals('total_amount >= 1500', 'having', $query);
    }

    public function testLimit()
    {
        $query = $this->createQuery();
        $query->limit(500);

        $this->assertAttributeEquals(500, 'limit', $query);
    }

    public function testOffset()
    {
        $query = $this->createQuery();
        $query->offset(20);

        $this->assertAttributeEquals(20, 'offset', $query);
    }

    public function testRange()
    {
        $query = $this->createQuery();
        $query->range(100, 500);

        $this->assertAttributeEquals(100, 'offset', $query);
        $this->assertAttributeEquals(500, 'limit', $query);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Query build error: using having without group by
     */
    public function testBuildQueryWithHavingWithoutGroupBy()
    {
        $query = $this->createQuery();
        $query
            ->having('total_amount >= 1500')
            ->build();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Query build error: fields number is not equals to 1
     */
    public function testBuildWithFetchColumnAndInvalidColumnsNumber()
    {
        $query = $this->createQuery();
        $query
            ->columns(['foo', 'bar'])
            ->fetchMode(\PDO::FETCH_COLUMN)
            ->build();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Query build error: fields number is not equals to 2
     */
    public function testBuildWithFetchKeyPairAndInvalidColumnsNumber()
    {
        $query = $this->createQuery();
        $query
            ->columns(['foo'])
            ->fetchMode(\PDO::FETCH_KEY_PAIR)
            ->build();
    }

    public function testBuild()
    {
        $factory = Factory::create($this->createPDOMock());

        $cte = [
            'regional_sales' => $factory
                ->select('orders')
                ->columns(['region', Field::create('SUM(amount)', 'total_sales')])
                ->groupBy(['region']),
            'top_regions' => $factory
                ->select('regional_sales')
                ->columns(['region'])
                ->andMore('total_sales', 1000)
        ];

        $query = $factory
            ->select('foo.bar')
            ->with($cte)
            ->columns(['field1', 'field2'])
            ->join('test', 't2', 't2.id = t1.user_id')
            ->andIn('field1', [3, 7, 9])
            ->orIsNull('field2')
            ->having('total_amount >= 1500')
            ->groupBy('region')
            ->orderBy(['region desc'])
            ->limit(1000)
            ->offset(150);

        $this->assertEquals('WITH regional_sales AS (SELECT region,SUM(amount) AS total_sales FROM orders t1 GROUP BY region), top_regions AS (SELECT region FROM regional_sales t1 WHERE total_sales > 1000) SELECT field1,field2 FROM foo.bar t1 INNER JOIN test t2 ON t2.id = t1.user_id WHERE field1 IN (3,7,9) OR field2 IS NULL GROUP BY region HAVING total_amount >= 1500 ORDER BY region desc LIMIT 1000 OFFSET 150', $query->build());
    }

    public function testExecute()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';
        $statement = new \PDOStatement();

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10);

        $this->assertInstanceOf(Select::class, $query->execute());
        $this->assertAttributeInstanceOf(\PDOStatement::class, 'statement', $query);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data fetch error: query must be executed before fetch data
     */
    public function testFetchAllBeforeExecute()
    {
        $query = $this->createQuery();
        $query->fetchAll();
    }

    public function testFetchAllObject()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            new \stdClass(),
            new \stdClass(),
        ];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchAll')
            ->with(\PDO::FETCH_CLASS, \stdClass::class)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_CLASS);

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertEquals($data, $query->fetchAll(\stdClass::class));
    }

    public function testFetchAllCallback()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            new \stdClass(),
            new \stdClass(),
        ];
        $callback = function () {};

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchAll')
            ->with(\PDO::FETCH_FUNC, $callback)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_FUNC);

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertEquals($data, $query->fetchAll($callback));
    }

    public function testFetchAllAssoc()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            ['foo' => 1, 'bar' => 2],
            ['foo' => 3, 'bar' => 4],
        ];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertEquals($data, $query->fetchAll());
    }

    public function testFetchAllKeyPair()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT field1,field2 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [
            [1 => 'foo'],
            [2 => 'bar'],
        ];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchAll')
            ->with(\PDO::FETCH_KEY_PAIR)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_KEY_PAIR);

        $query
            ->columns(['field1', 'field2'])
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertEquals($data, $query->fetchAll());
    }

    public function testFetchAllColumn()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT field1 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 10';

        $data = [1, 2];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_COLUMN);

        $query
            ->columns('field1')
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(10)
            ->execute();

        $this->assertEquals($data, $query->fetchAll());
    }

    public function testFetchOneObject()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data = new \stdClass();

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchObject')
            ->with(\stdClass::class)
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_CLASS);

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertEquals($data, $query->fetchOne(\stdClass::class));
    }

    public function testFetchOneAssoc()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT * FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data =['foo' => 1, 'bar' => 2];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetch')
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');

        $query
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertEquals($data, $query->fetchOne());
    }

    public function testFetchOneColumn()
    {
        $pdo = $this->createPDOMock();

        $query = 'SELECT field1 FROM foo.bar t1 WHERE field1 IN (3,7,9) ORDER BY field2 desc LIMIT 1';

        $data = 1;

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $statement
            ->method('fetchColumn')
            ->willReturn($data);

        $pdo
            ->method('prepare')
            ->with($query)
            ->willReturn($statement);

        $query = new Select($pdo, 'foo.bar');
        $query->fetchMode(\PDO::FETCH_COLUMN);

        $query
            ->columns('field1')
            ->andIn('field1', [3, 7, 9])
            ->orderBy(['field2 desc'])
            ->limit(1)
            ->execute();

        $this->assertEquals($data, $query->fetchOne());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data fetch error: query must be executed before fetch data
     */
    public function testFetchOneBeforeExecute()
    {
        $query = $this->createQuery();
        $query->fetchOne();
    }

    private function createQuery()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        return new Select($pdo, $table);
    }
}