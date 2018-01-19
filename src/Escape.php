<?php

namespace MediaTech\Query;


/**
 * Trait Escape
 * @package MediaTech\Query
 */
trait Escape
{
    /**
     * @param string $value
     * @param bool $quote
     * @return string
     */
    protected function escapeIdentifier(string $value, bool $quote = true): string
    {
        $str = preg_replace('/[^\.0-9a-z_]/i', '', $value);
        if ($str !== trim($value)) {
            throw new \InvalidArgumentException('Invalid identifier: invalid characters supplied');
        }

        if (preg_match('/^[0-9]/', $str)) {
            throw new \InvalidArgumentException('Invalid identifier: must begin with a letter or underscore');
        }

        return $quote ? '"' . $str . '"' : $str;
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
}