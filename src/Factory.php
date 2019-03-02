<?php

namespace Misantron\QueryBuilder;

/**
 * Class Factory.
 */
class Factory
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @param Server $server
     */
    private function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param Server $server
     *
     * @return Factory
     */
    public static function create(Server $server): Factory
    {
        return new static($server);
    }

    /**
     * @param Server $server
     *
     * @return Factory
     */
    public function setServer(Server $server): Factory
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @param string $table
     *
     * @return Query\Select
     */
    public function select(string $table): Query\Select
    {
        return new Query\Select($this->server, $table);
    }

    /**
     * @return Query\Update
     */
    public function update(): Query\Update
    {
        return new Query\Update($this->server);
    }

    /**
     * @param string $table
     *
     * @return Query\Delete
     */
    public function delete(string $table): Query\Delete
    {
        return new Query\Delete($this->server, $table);
    }

    /**
     * @param string $table
     *
     * @return Query\Insert
     */
    public function insert(string $table): Query\Insert
    {
        return new Query\Insert($this->server, $table);
    }
}
