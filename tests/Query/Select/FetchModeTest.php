<?php

namespace MediaTech\Query\Tests\Query\Select;


use MediaTech\Query\Query\Select\FetchMode;
use MediaTech\Query\Tests\BaseTestCase;

class FetchModeTest extends BaseTestCase
{
    public function testGetKeys()
    {
        $keys = [
            FetchMode::OBJECT,
            FetchMode::ASSOC,
            FetchMode::KEY_VALUE,
            FetchMode::COLUMN,
            FetchMode::CALLBACK,
            FetchMode::COLUMN_TO_ARRAY,
        ];

        $this->assertEquals($keys, FetchMode::getKeys());
    }
}