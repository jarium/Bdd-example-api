<?php

namespace app\Exception;

class BadRequestException extends ApiException
{
    public function __construct(string $message, string $customErrorCode)
    {
        parent::__construct(
            $message,
            $customErrorCode,
            400
        );
    }
}