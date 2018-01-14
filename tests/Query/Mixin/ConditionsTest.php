<?php

namespace MediaTech\Query\Tests\Query\Mixin;


use MediaTech\Query\Query\Select;
use MediaTech\Query\Tests\BaseTestCase;

class ConditionsTest extends BaseTestCase
{
    public function testBuildConditions()
    {
        $pdo = $this->createPDOMock();
        $table = 'foo.bar';

        $query = new Select($pdo, $table);

        $query
            ->beginGroup()
            ->andEquals('col1', false)
            ->orLessOrEquals('col1', 15)
            ->endGroup()
            ->andIn('col2', [33, 34])
            ->orBetween('col3', [5, 8])
            ->orIsNull('col4');

        $this->assertEquals("SELECT * FROM foo.bar t1 WHERE ( col1 = false OR col1 <= 15 ) AND col2 IN (33,34) OR col3 BETWEEN 5 AND 8 OR col4 IS NULL", $query->build());
    }
}