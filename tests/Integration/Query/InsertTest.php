<?php

namespace Misantron\QueryBuilder\Tests\Integration\Query;

use Misantron\QueryBuilder\Tests\Integration\IntegrationTestCase;

class InsertTest extends IntegrationTestCase
{
    public function testInsertSingleRow()
    {
        $query = $this->getFactory()->insert('foo.products');

        $response = $query
            ->values([
                'sku' => 'S12T-Gec-RS',
                'name' => 'Gecko Tee',
                'status_id' => 1,
                'regular_price' => 20.50,
                'category_id' => 12,
                'quantity' => 145,
                'taxable' => false,
                'tag_ids' => [5,8],
                'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ])
            ->execute()
            ->getInsertedRow();

        $this->assertArrayHasKey('sku', $response);
        $this->assertEquals('S12T-Gec-RS', $response['sku']);
        $this->assertArrayHasKey('status_id', $response);
        $this->assertEquals(1, $response['status_id']);
    }

    public function testInsertMultipleRows()
    {
        $query = $this->getFactory()->insert('foo.products');

        $response = $query
            ->values([
                [
                    'sku' => 'S12T-Gec-RS',
                    'name' => 'Gecko Tee Red',
                    'status_id' => 1,
                    'regular_price' => 20.50,
                    'category_id' => 12,
                    'quantity' => 145,
                    'taxable' => false,
                    'tag_ids' => [5,8],
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                [
                    'sku' => 'S12T-Gec-GS',
                    'name' => 'Gecko Tee Green',
                    'status_id' => 1,
                    'regular_price' => 20.70,
                    'category_id' => 12,
                    'quantity' => 180,
                    'taxable' => false,
                    'tag_ids' => [5,6],
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ]
            ])
            ->execute()
            ->getInsertedRows();

        $this->assertInternalType('array', $response);
        $this->assertCount(2, $response);
    }

    public function testInsertFromRows()
    {
        $query = $this->getFactory()->insert('foo.tags');

        $query
            ->values([
                [
                    'name' => 'Green',
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Red',
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s')
                ]
            ])
            ->execute();

        $select = $this->getFactory()
            ->select('foo.tags')
            ->columns(['name', 'inserted_at']);

        $response = $this->getFactory()
            ->insert('foo.tags')
            ->fromRows($select)
            ->execute()
            ->getInsertedRows();

        $this->assertInternalType('array', $response);
        $this->assertCount(2, $response);

        $select = $this->getFactory()
            ->select('foo.tags')
            ->execute();

        $this->assertEquals(4, $select->rowsCount());
    }

    protected function tearDown()
    {
        $this->getFactory()
            ->delete('foo.products')
            ->execute();

        $this->getFactory()
            ->delete('foo.tags')
            ->execute();
    }
}