<?php

namespace MediaTech\Query;


class Type extends AbstractEnum
{
    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;
}