<?php

namespace MediaTech\Query\Query\Select;


class FetchMode
{
    const OBJECT = 1;
    const ASSOC = 2;
    const BY_ID = 3;
    const KEY_VALUE = 4;
    const COLUMN = 5;
    const CALLBACK = 6;
    const COLUMN_TO_ARRAY = 7;

    /**
     * @var array
     */
    private static $cache;

    /**
     * @throws \BadMethodCallException
     */
    final private function __construct()
    {
        throw new \BadMethodCallException('Non static call is disabled');
    }

    /**
     * @return array
     */
    final public static function getKeys(): array
    {
        if (!isset(static::$cache)) {
            $class = new \ReflectionClass(static::class);
            static::$cache = array_values($class->getConstants());
        }
        return static::$cache;
    }
}