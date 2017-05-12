<?php

namespace MediaTech\Query;


abstract class AbstractEnum
{
    /**
     * @var array
     */
    protected static $cache = [];

    final private function __construct()
    {
        throw new \BadMethodCallException('Non static call is disabled.');
    }

    /**
     * @return array
     */
    final public static function getKeys()
    {
        $hash = crc32(static::class);
        if (!isset(static::$cache[$hash])) {
            $class = new \ReflectionClass(static::class);
            static::$cache[$hash] = array_values($class->getConstants());
        }
        return static::$cache[$hash];
    }
}