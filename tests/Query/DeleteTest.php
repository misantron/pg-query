<?php

namespace MediaTech\Query\Tests\Query;


use MediaTech\Query\Query\Delete;
use MediaTech\Query\Query\Mixin\Filter\FilterGroup;
use MediaTech\Query\Tests\BaseTestCase;

class DeleteTest extends BaseTestCase
{
    public function testConstructor()
    {
        $query = $this->createQuery();

        $this->assertAttributeInstanceOf(\PDO::class, 'pdo', $query);
        $this->assertAttributeInstanceOf(FilterGroup::class, 'filters', $query);
        $this->assertAttributeEquals('foo.bar', 'table', $query);
    }

    public function testBuildWithoutConditions()
    {
        $query = $this->createQuery();

        $this->assertEquals('DELETE FROM foo.bar', $query->build());
    }

    public function testBuild()
    {
        $pdo = $this->createPDOMock();

        $pdo
            ->method('quote')
            ->withConsecutive(['test', \PDO::PARAM_STR])
            ->willReturnOnConsecutiveCalls("'test'");

        $query = $this->createQuery($pdo);
        $query
            ->andEquals('col1', 1)
            ->andEquals('col2', 'test');

        $this->assertEquals("DELETE FROM foo.bar WHERE col1 = 1 AND col2 = 'test'", $query->build());
    }

    private function createQuery($pdo = null)
    {
        $pdo = $pdo ?? $this->createPDOMock();
        $table = 'foo.bar';

        return new Delete($pdo, $table);
    }
}