<?php

if (!function_exists('class_basename')) {

    /**
     * @param string $className
     *
     * @return string
     */
    function class_basename(string $className)
    {
        return substr(strrchr($className, '\\'), 1);
    }
}
