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
        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is empty');
        }

        $this->pdo = $pdo;
        $this->table = $this->escapeIdentifier($table, false);
    }

    /**
     * @return string
     */
    abstract public function build(): string;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    protected function isJson($value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function escapeIdentifier(string $value, bool $quote = true): string
    {
        $str = preg_replace('/[^\.0-9a-z_]/i', '', $value);
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
            $type = $this->isIntegerArray($value) ? 'integer': 'string';
            $escaped = $this->escapeArray($value, $type);
        } else {
            $escaped = $this->pdo->quote($value, \PDO::PARAM_STR);
        }
        return $escaped;
    }

    /**
     * @param array $items
     * @param string $type
     * @return string
     */
    private function escapeArray(array $items, string $type = 'integer'): string
    {
        $cast = $type === 'integer' ? 'INTEGER[]' : 'VARCHAR[]';

        if (empty($items)) {
            return 'ARRAY[]::' . $cast;
        }
        if ($type === 'string') {
            $items = array_map(function (string $value) {
                return "'" . $value . "'";
            }, $items);
        }
        return 'ARRAY[' . implode(',', $items) . ']::' . $cast;
    }

    /**
     * @param array $array
     * @return bool
     */
    private function isIntegerArray(array $array): bool
    {
        $filtered = array_filter($array, function ($value) {
            return !is_int($value);
        });
        return empty($filtered);
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
            return $this->escapeIdentifier(trim($field), false);
        }, $fields));
    }
}