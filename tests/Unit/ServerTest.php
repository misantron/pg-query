<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Configuration;
use Misantron\QueryBuilder\Server;

class ServerTest extends UnitTestCase
{
    public function testConstructor()
    {
        $dsn = 'pgsql:host=localhost;port=5432;dbname=test_db;user=test_user;password=test';

        $config = Configuration::createFromDsn($dsn, [], '9.5');
        $server = new Server($config);

        $this->assertAttributeInstanceOf(Configuration::class, 'configuration', $server);
        $this->assertSame('9.5', $server->version());
    }
}
