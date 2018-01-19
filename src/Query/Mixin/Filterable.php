<?php

namespace MediaTech\Query\Query\Mixin;


interface Filterable
{
    /**
     * @return Filterable
     */
    public function beginGroup();

    /**
     * @return Filterable
     */
    public function andGroup();

    /**
     * @return Filterable
     */
    public function orGroup();

    /**
     * @return Filterable
     */
    public function endGroup();

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function equals(string $column, $value);

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function andEquals(string $column, $value);

    /**
     * @param string $column
     * @param mixed $value
     *
     * @return Filterable
     */
    public function orEquals(string $column, $value);
}