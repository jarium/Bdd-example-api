<?php

namespace app\Exception;

class UnauthorizedException extends ApiException
{
    public function __construct(string $message, string $customErrorCode)
    {
        parent::__construct(
            $message,
            $customErrorCode,
            401
        );
    }
}