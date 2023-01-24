<?php

namespace app\helpers;

use Throwable;

class LoggerHelper
{
    public static function getThrowableDetails(Throwable $t): string
    {
        return ' ErrorCode : ' . $t->getCode() . ', ErrorMessage : ' . $t->getMessage() .
            ', ErrorClass: ' . get_class($t) . ', ErrorFile: ' . $t->getFile() . ':' . $t->getLine() .
            ', Trace: ' . $t->getTraceAsString() . ', File: ' . __FILE__ . ':' . __LINE__;
    }
}