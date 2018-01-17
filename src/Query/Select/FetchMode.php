<?php

namespace MediaTech\Query\Query\Select;


class FetchMode
{
    const OBJECT = 1;
    const ASSOC = 2;
    const KEY_VALUE = 3;
    const COLUMN = 4;
    const CALLBACK = 5;
    const COLUMN_TO_ARRAY = 6;

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