<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder;

use Misantron\QueryBuilder\Assert\ServerAssert;
use PDO;

/**
 * Class Server.
 */
class Server
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $credentials;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @var array
     */
    private static $defaultCredentials = [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => null,
        'user' => null,
        'password' => null,
    ];

    public function __construct(array $credentials, array $options = [], ?string $version = null)
    {
        $default = self::$defaultCredentials;

        $this->credentials = array_filter(array_replace($default, array_intersect_key($credentials, $default)));

        $this->options = [];
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        $this->version = $version;
    }

    /**
     * @param mixed $value
     */
    public function setOption(int $key, $value): Server
    {
        ServerAssert::validConnectionOption($key);

        $this->options[$key] = $value;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function pdo(): PDO
    {
        $this->initialize();

        return $this->pdo;
    }

    private function initialize(): void
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO($this->createDsnFromCredentials());
            foreach ($this->options as $attribute => $value) {
                $this->pdo->setAttribute($attribute, $value);
            }
        }
    }

    private function createDsnFromCredentials(): string
    {
        $credentials = $this->credentials;

        $dsn = array_reduce(
            array_keys($credentials),
            static function (string $carry, string $key) use ($credentials) {
                return $carry . $key . '=' . $credentials[$key] . ';';
            },
            'pgsql:'
        );

        return rtrim($dsn, ';');
    }
}
