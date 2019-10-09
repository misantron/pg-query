<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Helper;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Exception\IdentifierException;
use Misantron\QueryBuilder\Exception\QueryParameterException;

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

        if (preg_match('/^[\d]/', $str)) {
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
                $escaped = (string) $value;
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
            case 'object':
                $escaped = $this->jsonEncode($value);
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
        $first = reset($values);
        if ($first === false) {
            return "'{}'";
        }

        if ($values !== array_values($values)) {
            return $this->jsonEncode($values);
        }

        $type = strtolower(gettype($first));
        $nonQuotedTypes = ['integer', 'double', 'boolean', 'null'];
        if (!in_array($type, $nonQuotedTypes, true)) {
            if ($type === 'string') {
                $values = array_map(static function (string $value) {
                    return '"' . str_replace('"', '\"', $value) . '"';
                }, $values);
            } else {
                throw QueryParameterException::unexpectedValues($type, $values);
            }
        }

        return "'{" . implode(',', $values) . "}'";
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
        $filtered = array_filter($array, static function ($value) {
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
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function jsonEncode($value): string
    {
        $encoded = json_encode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw QueryParameterException::encodingError(json_last_error_msg());
        }
        $trimmed = stripslashes(trim($encoded, '"'));

        return "'{$trimmed}'";
    }
}
