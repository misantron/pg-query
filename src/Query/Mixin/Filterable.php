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
}