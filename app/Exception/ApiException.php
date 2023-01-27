<?php

namespace app\Exception;

use Exception;

class ApiException extends Exception
{
    private string $customErrorCode;

    public function __construct($message = "", $customErrorCode = null, $errorCode = 500)
    {
        parent::__construct($message, $errorCode);
        $this->customErrorCode = $customErrorCode;
    }

    /**
     * @return string
     */
    public function getCustomErrorCode(): string
    {
        return $this->customErrorCode;
    }
}