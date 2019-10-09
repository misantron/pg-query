<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

trait AssertObjectProperty
{
    /**
     * @param mixed  $expected
     * @param string $attributeName
     * @param object $actual
     */
    public function assertPropertySame($expected, string $attributeName, $actual): void
    {
        static::assertSame($expected, $this->getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param string $expected
     * @param string $attributeName
     * @param object $actual
     */
    public function assertPropertyInstanceOf(string $expected, string $attributeName, $actual): void
    {
        static::assertInstanceOf($expected, $this->getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param string $attributeName
     * @param object $actual
     */
    public function assertPropertyNull(string $attributeName, $actual): void
    {
        static::assertNull($this->getObjectPropertyValue($actual, $attributeName));
    }

    /**
     * @param object $obj
     * @param string $name
     *
     * @return mixed
     */
    private function getObjectPropertyValue($obj, string $name)
    {
        try {
            $class = new ReflectionClass($obj);
            $method = $class->getProperty($name);
            $method->setAccessible(true);
        } catch (ReflectionException $e) {
            throw new RuntimeException('Unable to get non-public property value');
        }

        return $method->getValue($obj);
    }
}
