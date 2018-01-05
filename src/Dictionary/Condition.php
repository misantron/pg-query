<?php

namespace MediaTech\Query\Dictionary;


class Condition extends Dictionary
{
    const EQUAL = '=';
    const NOT_EQUAL = '!=';
    const MORE = '>';
    const MORE_OR_EQUAL = '>=';
    const LESS = '<';
    const LESS_OR_EQUAL = '<=';
    const ANY = 'ANY';
    const BETWEEN = 'BETWEEN';
    const IN = 'IN';
    const NOT_IN = 'NOT IN';
    const IS_NULL = 'IS NULL';
    const IS_NOT_NULL = 'IS NOT NULL';
    const IN_JSON = '@>';
    const IN_CIDR = '<<=';
}