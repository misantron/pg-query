<?php

namespace MediaTech\Query\Query;


class Delete extends Query
{
    /**
     * @var string
     */
    private $alias;

    public function alias(string $value)
    {
        $this->alias = $this->escapeIdentifier($value, false);

        return $this;
    }

    public function build(): string
    {
        return '';
    }
}