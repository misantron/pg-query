<?php

namespace MediaTech\Query\Helper;


/**
 * Trait Escape
 * @package MediaTech\Query
 */
trait Escape
{
    /**
     * @param string $value
     * @return string
     */
    protected function escapeIdentifier(string $value): string
    {
        $str = preg_replace('/[^\.0-9a-z_]/i', '', $value);
        if ($str !== trim($value)) {
            throw new \InvalidArgumentException('Invalid identifier: invalid characters supplied');
        }

        if (preg_match('/^[0-9]/', $str)) {
            throw new \InvalidArgumentException('Invalid identifier: must begin with a letter or underscore');
        }

        return $str;
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
            $escaped = $this->quote($value);
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
                return $this->quote($value);
            }, $values);
        }
        return 'ARRAY[' . implode(',', $values) . ']::' . $cast;
    }

    /**
     * @param array $items
     * @return string
     */
    protected function escapeList(array $items): string
    {
        $type = $this->isIntegerArray($items) ? 'integer': 'string';
        $filtered = array_filter($items);
        if (empty($filtered)) {
            throw new \InvalidArgumentException('Value list is empty');
        }
        if ($type === 'string') {
            $filtered = array_map(function (string $item) {
                return $this->quote($item);
            }, $filtered);
        }
        return implode(',', $filtered);
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
     * @param mixed $value
     * @return string
     */
    protected function quote($value): string
    {
        return "'" . str_replace("'", "''", (string)$value) . "'";
    }
}