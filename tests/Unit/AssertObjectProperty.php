<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

trait AssertObjectProperty
{
    /**
     * @param mixed  $expected
     * @param string $attributeName
     * @param object $actual
     */
    public static function assertPropertySame($expected, string $attributeName, $actual): void
    {
        self::assertSame($expected, self::getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param string $expected
     * @param string $attributeName
     * @param object $actual
     */
    public static function assertPropertyInstanceOf(string $expected, string $attributeName, $actual): void
    {
        self::assertInstanceOf($expected, self::getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param string $attributeName
     * @param object $actual
     */
    public static function assertPropertyNull(string $attributeName, $actual): void
    {
        self::assertNull(self::getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param object $obj
     * @param string $name
     *
     * @return mixed
     */
    private static function getObjectPropertyValue($obj, string $name)
    {
        try {
            $class = new \ReflectionClass($obj);
            $method = $class->getProperty($name);
            $method->setAccessible(true);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('Unable to get non-public property value');
        }

        return $method->getValue($obj);
    }
}
