<?php

namespace app\helpers;

class TimeHelper
{
    public static function now(): string
    {
        return TEST_ENV ? TEST_TIME : gmdate('Y-m-d H:i:s');
    }
}