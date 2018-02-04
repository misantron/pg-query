<?php

$schema = file_get_contents(__DIR__ . '/schema.sql');

if ($schema === false) {
    throw new \InvalidArgumentException('Unable to load database schema');
}

$db = new \PDO('pgsql:host=localhost;port=5432;dbname=test', 'postgres', '');
$db->exec($schema);