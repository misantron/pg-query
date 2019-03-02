<?php

namespace Misantron\QueryBuilder\Tests\Unit;

use Misantron\QueryBuilder\Configuration;

class ConfigurationTest extends UnitTestCase
{
    public function testCreateFromDsn()
    {
        $dsn = 'pgsql:host=192.283.16.1;port=6543;dbname=dbtest;user=test;password=pass';

        $config = Configuration::createFromDSN($dsn, [], '9.3');

        $this->assertAttributeSame($dsn, 'dsn', $config);
        $this->assertAttributeSame([], 'options', $config);
        $this->assertAttributeSame('9.3', 'version', $config);

    }

    public function testCreateFromCredentialsWithDefaultHostAndPort()
    {
        $credentials = [
            'version' => '9.5',
            'dbname' => 'test_db',
            'user' => 'test_user',
            'password' => 'test',
        ];

        $config = Configuration::createFromCredentials($credentials, []);

        $dsn = 'pgsql:host=localhost;port=5432;dbname=test_db;user=test_user;password=test';

        $this->assertAttributeSame($dsn, 'dsn', $config);
        $this->assertAttributeSame('9.5', 'version', $config);
        $this->assertAttributeSame([], 'options', $config);
    }

    public function testCreateFromCredentials()
    {
        $credentials = [
            'version' => '9.4',
            'host' => '192.283.1.14',
            'port' => '7001',
            'dbname' => 'test_db',
            'user' => 'test_user',
            'password' => 'test',
        ];

        $config = Configuration::createFromCredentials($credentials);

        $dsn = 'pgsql:host=192.283.1.14;port=7001;dbname=test_db;user=test_user;password=test';

        $this->assertAttributeSame($dsn, 'dsn', $config);
        $this->assertAttributeSame('9.4', 'version', $config);
    }

    public function testDsn()
    {
        $config = $this->createConfiguration();

        $this->assertSame('pgsql:host=192.283.16.1;port=6543;dbname=dbtest;user=test;password=pass', $config->dsn());
    }

    public function testVersion()
    {
        $config = $this->createConfiguration();

        $this->assertSame('9.3', $config->version());
    }

    public function testOptions()
    {
        $config = $this->createConfiguration();

        $this->assertSame([], $config->options());
    }

    private function createConfiguration(): Configuration
    {
        $dsn = 'pgsql:host=192.283.16.1;port=6543;dbname=dbtest;user=test;password=pass';
        $version = '9.3';
        $options = [];

        return Configuration::createFromDSN($dsn, $options, $version);
    }
}