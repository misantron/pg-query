<?php

namespace Misantron\QueryBuilder;

/**
 * Class Server.
 */
class Server
{
    private const AVAILABLE_OPTIONS = [
        \PDO::ATTR_AUTOCOMMIT,
        \PDO::ATTR_TIMEOUT,
        \PDO::ATTR_ERRMODE,
        \PDO::ATTR_CASE,
        \PDO::ATTR_CURSOR_NAME,
        \PDO::ATTR_CURSOR,
        \PDO::ATTR_PERSISTENT,
        \PDO::ATTR_STATEMENT_CLASS,
        \PDO::ATTR_FETCH_TABLE_NAMES,
        \PDO::ATTR_FETCH_CATALOG_NAMES,
        \PDO::ATTR_STRINGIFY_FETCHES,
        \PDO::ATTR_MAX_COLUMN_LEN,
        \PDO::ATTR_EMULATE_PREPARES,
        \PDO::ATTR_DEFAULT_FETCH_MODE,
    ];

    /**
     * @var \PDO
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

    /**
     * @param array       $credentials
     * @param array       $options
     * @param string|null $version
     */
    public function __construct(array $credentials, array $options = [], ?string $version = null)
    {
        $default = static::$defaultCredentials;

        $this->credentials = array_filter(array_replace($default, array_intersect_key($credentials, $default)));

        $this->options = [];
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        $this->version = $version;
    }

    /**
     * @param int   $key
     * @param mixed $value
     * @return Server
     */
    public function setOption(int $key, $value): Server
    {
        if (!in_array($key, self::AVAILABLE_OPTIONS)) {
            throw new \InvalidArgumentException('Unknown connection option provided');
        }
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return \PDO
     */
    public function pdo(): \PDO
    {
        $this->initialize();

        return $this->pdo;
    }

    private function initialize(): void
    {
        if ($this->pdo === null) {
            $this->pdo = new \PDO($this->createDsnFromCredentials());
            foreach ($this->options as $attribute => $value) {
                $this->pdo->setAttribute($attribute, $value);
            }
        }
    }

    /**
     * @return string
     */
    private function createDsnFromCredentials(): string
    {
        $credentials = $this->credentials;

        $dsn = array_reduce(
            array_keys($credentials),
            function (string $carry, string $key) use ($credentials) {
                return $carry . $key . '=' . $credentials[$key] . ';';
            },
            'pgsql:'
        );

        return rtrim($dsn, ';');
    }
}
