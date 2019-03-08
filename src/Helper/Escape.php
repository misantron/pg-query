<?php

namespace Misantron\QueryBuilder\Helper;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Exception\IdentifierException;

/**
 * Trait Escape.
 */
trait Escape
{
    /**
     * @param string $value
     *
     * @return string
     *
     * @throws IdentifierException
     */
    protected function escapeIdentifier(string $value): string
    {
        $str = preg_replace('/[^\.0-9a-z_]/i', '', $value);
        if ($str !== trim($value)) {
            throw IdentifierException::supplyInvalidChar();
        }

        if (preg_match('/^[0-9]/', $str)) {
            throw IdentifierException::beginFromInvalidChar();
        }

        return $str;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function escapeValue($value): string
    {
        switch (strtolower(gettype($value))) {
            case 'integer':
            case 'double':
                $escaped = $value;
                break;
            case 'boolean':
                $escaped = $value ? 'true' : 'false';
                break;
            case 'null':
                $escaped = 'null';
                break;
            case 'array':
                $escaped = $this->escapeArray($value);
                break;
            default:
                $escaped = $this->quote($value);
        }

        return $escaped;
    }

    /**
     * @param array $values
     *
     * @return string
     */
    protected function escapeArray(array $values): string
    {
        $type = $this->isIntegerArray($values) ? 'integer' : 'string';
        $cast = $type === 'integer' ? 'INTEGER[]' : 'VARCHAR[]';

        if ($type === 'string') {
            $values = array_map(function (string $value) {
                return $this->quote($value);
            }, $values);
        }

        return 'ARRAY[' . implode(',', $values) . ']::' . $cast;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    protected function escapeList(array $items): array
    {
        $type = $this->isIntegerArray($items) ? 'integer' : 'string';
        $filtered = array_unique(array_filter($items));

        QueryAssert::valuesNotEmpty($filtered);

        if ($type === 'string') {
            $filtered = array_map(function (string $item) {
                return $this->quote($item);
            }, $filtered);
        }

        return $filtered;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    protected function isIntegerArray(array $array): bool
    {
        $filtered = array_filter($array, function ($value) {
            return filter_var($value, FILTER_VALIDATE_INT) === false;
        });

        return empty($filtered);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function quote($value): string
    {
        return "'" . str_replace("'", "''", (string)$value) . "'";
    }
}
