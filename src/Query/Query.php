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

    /**
     * @param string $value
     * @param bool $quote
     * @return string
     */
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

    /**
     * @param mixed $value
     * @return string
     */
    protected function escapeValue($value): string
    {
        if (is_numeric($value)) {
            $escaped = $value;
        } elseif (is_null($value)) {
            $escaped = 'null';
        } elseif (is_bool($value)) {
            $escaped = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $escaped = $this->escapeArray($value);
        } else {
            $escaped = $this->pdo->quote($value, \PDO::PARAM_STR);
        }
        return $escaped;
    }

    /**
     * @param array $values
     * @return string
     */
    protected function escapeArray(array $values): string
    {
        $type = $this->isIntegerArray($values) ? 'integer': 'string';
        $cast = $type === 'integer' ? 'INTEGER[]' : 'VARCHAR[]';

        if (empty($values)) {
            return 'ARRAY[]::' . $cast;
        }
        if ($type === 'string') {
            $values = array_map(function (string $value) {
                return "'" . str_replace("'", "''", $value) . "'";
            }, $values);
        }
        return 'ARRAY[' . implode(',', $values) . ']::' . $cast;
    }

    /**
     * @param array $array
     * @return bool
     */
    protected function isIntegerArray(array $array): bool
    {
        $filtered = array_filter($array, function ($value) {
            return !is_int($value);
        });
        return empty($filtered);
    }

    /**
     * @param array|string $items
     * @return array
     */
    protected function parseList($items): array
    {
        if (is_string($items)) {
            $items = explode(',', $items);
        }

        return array_filter(array_map(function (string $item) {
            return $this->escapeIdentifier(trim($item), false);
        }, $items));
    }
}