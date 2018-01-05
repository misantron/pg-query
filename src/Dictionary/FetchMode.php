<?php

namespace MediaTech\Query\Dictionary;


class FetchMode extends Dictionary
{
    const OBJECT = 1;
    const ASSOC = 2;
    const BY_ID = 3;
    const KEY_VALUE = 4;
    const COLUMN = 5;
    const CALLBACK = 6;
    const COLUMN_TO_ARRAY = 7;
}