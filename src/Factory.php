<?php

namespace Misantron\QueryBuilder;

use Misantron\QueryBuilder\Query;

/**
 * Class Factory
 * @package Misantron\QueryBuilder
 */
class Factory
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->setPDO($pdo);
    }

    /**
     * @param \PDO $pdo
     * @return Factory
     */
    public static function create(\PDO $pdo): Factory
    {
        return new static($pdo);
    }

    /**
     * @param \PDO $pdo
     * @return Factory
     */
    public function setPDO(\PDO $pdo): Factory
    {
        $this->pdo = $pdo;

        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @param string $table
     * @return Query\Select
     */
    public function select(string $table): Query\Select
    {
        return new Query\Select($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Query\Update
     */
    public function update(string $table): Query\Update
    {
        return new Query\Update($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Query\Delete
     */
    public function delete(string $table): Query\Delete
    {
        return new Query\Delete($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Query\Insert
     */
    public function insert(string $table): Query\Insert
    {
        return new Query\Insert($this->pdo, $table);
    }
}