<?php

$schema = file_get_contents(dirname(__FILE__) . '/schema.sql');

$db = new \PDO('pgsql:host=localhost;port=5432;dbname=test', 'postgres', '');
$db->exec($schema);