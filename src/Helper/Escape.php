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
        if (!\in_array($type, $nonQuotedTypes, true)) {
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

    protected function isIntegerArray(array $array): bool
    {
        $filtered = array_filter($array, static function ($value) {
            return filter_var($value, FILTER_VALIDATE_INT) === false;
        });

        return empty($filtered);
    }

    protected function quote($value): string
    {
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

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
