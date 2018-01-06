<?php

namespace MediaTech\Query;


use MediaTech\Query\Query\Delete;
use MediaTech\Query\Query\Insert;
use MediaTech\Query\Query\Select;
use MediaTech\Query\Query\Update;

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
    public static function create(\PDO $pdo)
    {
        return new static($pdo);
    }

    /**
     * @param \PDO $pdo
     * @return Factory
     */
    public function setPDO(\PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @param string $table
     * @return Select
     */
    public function select(string $table)
    {
        return new Select($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Update
     */
    public function update(string $table)
    {
        return new Update($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Delete
     */
    public function delete(string $table)
    {
        return new Delete($this->pdo, $table);
    }

    /**
     * @param string $table
     * @return Insert
     */
    public function insert(string $table)
    {
        return new Insert($this->pdo, $table);
    }
}