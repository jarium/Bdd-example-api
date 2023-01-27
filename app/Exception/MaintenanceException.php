<?php

namespace app\Exception;

use app\Api\ConstantError;

class MaintenanceException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'maintenance, please visit us later',
            ConstantError::MAINTENANCE_ERROR,
            503
        );
    }
}