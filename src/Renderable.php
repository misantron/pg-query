<?php

namespace MediaTech\Query;


interface Renderable
{
    /**
     * @return string
     */
    public function build(): string;
}