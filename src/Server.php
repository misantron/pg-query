<?php

namespace Misantron\QueryBuilder;

/**
 * Class Server.
 */
class Server
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string|null
     */
    public function version(): ?string
    {
        return $this->configuration->version();
    }

    /**
     * @return \PDO
     */
    public function pdo(): \PDO
    {
        $this->initialize();

        return $this->pdo;
    }

    /**
     * @return void
     */
    private function initialize(): void
    {
        if ($this->pdo === null) {
            $this->pdo = new \PDO($this->configuration->dsn());
            foreach ($this->configuration->options() as $attribute => $value) {
                $this->pdo->setAttribute($attribute, $value);
            }
        }
    }
}
