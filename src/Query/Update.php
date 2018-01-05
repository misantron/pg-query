<?php

namespace MediaTech\Query\Query;


class Update extends Query
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var array
     */
    private $set;

    public function alias(string $value)
    {
        $this->alias = $this->escapeIdentifier($value, false);

        return $this;
    }

    public function set(array $data)
    {
        foreach ($data as $field => $value) {
            $this->set[$this->escapeIdentifier($field)] = $this->escapeValue($value);
        }
        return $this;
    }

    public function build(): string
    {
        return '';
    }
}