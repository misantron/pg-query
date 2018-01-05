<?php

namespace MediaTech\Query\Dictionary;


abstract class Dictionary
{
    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @throws \BadMethodCallException
     */
    final private function __construct()
    {
        throw new \BadMethodCallException('Non static call is disabled.');
    }

    /**
     * @return array
     */
    final public static function getKeys(): array
    {
        $hash = crc32(static::class);
        if (!isset(static::$cache[$hash])) {
            $class = new \ReflectionClass(static::class);
            static::$cache[$hash] = array_values($class->getConstants());
        }
        return static::$cache[$hash];
    }
}