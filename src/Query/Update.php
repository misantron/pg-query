<?php

namespace MediaTech\Query\Query;


use MediaTech\Query\Query\Mixin\Conditions;

class Update extends Query
{
    use Conditions;

    /**
     * @var array
     */
    private $set;

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