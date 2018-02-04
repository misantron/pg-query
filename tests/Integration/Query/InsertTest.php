<?php

namespace MediaTech\Query\Tests\Integration\Query;


use MediaTech\Query\Tests\Integration\BaseTestCase;

class InsertTest extends BaseTestCase
{
    public function testInsertSingleRow()
    {
        $query = $this->getFactory()->insert('products');

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
        $query = $this->getFactory()->insert('products');

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

    protected function tearDown()
    {
        $this->getFactory()
            ->delete('products')
            ->execute();
    }
}