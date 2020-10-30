<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Integration\Query;

use Misantron\QueryBuilder\Tests\Integration\IntegrationTestCase;

class InsertTest extends IntegrationTestCase
{
    public function testInsertSingleRow(): void
    {
        $query = $this->getFactory()->insert('public.products');

        $response = $query
            ->values([
                'sku' => 'S12T-Gec-RS',
                'name' => 'Gecko Tee',
                'status_id' => 1,
                'regular_price' => 20.50,
                'category_id' => 12,
                'quantity' => 145,
                'taxable' => false,
                'tag_ids' => [5, 8],
                'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ])
            ->returning([
                'sku',
                'status_id',
            ])
            ->execute()
            ->getInsertedRow();

        self::assertArrayHasKey('sku', $response);
        self::assertSame('S12T-Gec-RS', $response['sku']);
        self::assertArrayHasKey('status_id', $response);
        self::assertEquals(1, $response['status_id']);
    }

    public function testInsertMultipleRows(): void
    {
        $query = $this->getFactory()->insert('public.products');

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
                    'tag_ids' => [5, 8],
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
                    'tag_ids' => [5, 6],
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
            ])
            ->returning([
                'sku',
                'name',
                'quantity',
            ])
            ->execute()
            ->getInsertedRows();

        self::assertCount(2, $response);
    }

    public function testInsertFromRows(): void
    {
        $query = $this->getFactory()->insert('public.tags');

        $query
            ->values([
                [
                    'name' => 'Green',
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Red',
                    'inserted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
            ])
            ->execute();

        $select = $this->getFactory()
            ->select('public.tags')
            ->columns(['name', 'inserted_at']);

        $response = $this->getFactory()
            ->insert('public.tags')
            ->fromRows($select)
            ->returning([
                'name', 'inserted_at',
            ])
            ->execute()
            ->getInsertedRows();

        self::assertCount(2, $response);

        $select = $this->getFactory()
            ->select('public.tags')
            ->execute();

        self::assertSame(4, $select->rowsCount());
    }

    protected function tearDown(): void
    {
        $this->getFactory()
            ->delete('public.products')
            ->execute();

        $this->getFactory()
            ->delete('public.tags')
            ->execute();
    }
}
