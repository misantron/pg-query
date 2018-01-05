<?php

namespace MediaTech\Query\Query;


abstract class Query
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(\PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $this->escapeIdentifier($table, false);
    }

    abstract public function build(): string;

    protected function arrayConvert(array $items, string $type = 'integer'): string
    {
        if (empty($items) || !is_array($items)) {
            return 'ARRAY[]::INTEGER[]';
        }
        return sprintf('ARRAY[%s]::INTEGER[]', implode(',', $items));
    }

    protected function isJson($value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function escapeIdentifier(string $value, bool $quote = true): string
    {
        $str = preg_replace('/[^0-9a-z_]/i', '', $value);
        if ($str !== trim($value)) {
            throw new \InvalidArgumentException('Invalid identifier: Invalid characters supplied.');
        }

        if (preg_match('/^[0-9]/', $str)) {
            throw new \InvalidArgumentException('Invalid identifier: Must begin with a letter or underscore.');
        }

        return $quote ? '"' . $str . '"' : $str;
    }

    protected function escapeValue($value)
    {
        if (is_numeric($value)) {
            $escaped = $value;
        } elseif (is_null($value)) {
            $escaped = 'null';
        } elseif (is_bool($value)) {
            $escaped = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $escaped = $this->arrayConvert($value);
        } else {
            $escaped = $this->pdo->quote($value, \PDO::PARAM_STR);
        }
        return $escaped;
    }

    /**
     * @param array|string $fields
     * @return array
     */
    protected function parseColumns($fields): array
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        return array_filter(array_map(function (string $field) {
            return $this->escapeIdentifier(trim($field));
        }, $fields));
    }
}