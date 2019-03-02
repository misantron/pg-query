<?php

namespace Misantron\QueryBuilder;

/**
 * Class Configuration.
 */
final class Configuration
{
    /**
     * @var string
     */
    private $dsn;

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
     * @param string      $dsn
     * @param array       $options
     * @param string|null $version
     */
    private function __construct(string $dsn, array $options, ?string $version)
    {
        $this->dsn = $dsn;
        $this->options = $options;
        $this->version = $version;
    }

    /**
     * @param string      $dsn
     * @param array       $options
     * @param string|null $version
     *
     * @return Configuration
     */
    public static function createFromDsn(string $dsn, array $options = [], ?string $version = null): Configuration
    {
        return new static($dsn, $options, $version);
    }

    /**
     * @param array $credentials
     * @param array $options
     *
     * @return Configuration
     */
    public static function createFromCredentials(array $credentials, array $options = []): Configuration
    {
        $version = null;
        if (isset($credentials['version'])) {
            $version = $credentials['version'];
            unset($credentials['version']);
        }

        $credentials = array_filter(array_replace(self::$defaultCredentials, $credentials));

        $dsn = self::buildDsnFromCredentials($credentials);

        return new static($dsn, $options, $version);
    }

    /**
     * @param array $credentials
     *
     * @return string
     */
    private static function buildDsnFromCredentials(array $credentials): string
    {
        $dsn = array_reduce(
            array_keys($credentials),
            function (string $carry, string $key) use ($credentials) {
                return $carry . $key . '=' . $credentials[$key] . ';';
            },
            'pgsql:'
        );

        return rtrim($dsn, ';');
    }

    /**
     * @return string
     */
    public function dsn(): string
    {
        return $this->dsn;
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return string|null
     */
    public function version(): ?string
    {
        return $this->version;
    }
}
