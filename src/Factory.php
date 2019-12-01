<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder;

/**
 * Class Factory.
 */
final class Factory
{
    /**
     * @var Server
     */
    private $server;

    private function __construct(Server $server)
    {
        $this->server = $server;
    }

    public static function create(Server $server): Factory
    {
        return new static($server);
    }

    public function setServer(Server $server): Factory
    {
        $this->server = $server;

        return $this;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function select(string $table): Query\Select
    {
        return new Query\Select($this->server, $table);
    }

    public function update(): Query\Update
    {
        return new Query\Update($this->server);
    }

    public function delete(string $table): Query\Delete
    {
        return new Query\Delete($this->server, $table);
    }

    public function insert(string $table): Query\Insert
    {
        return new Query\Insert($this->server, $table);
    }
}
