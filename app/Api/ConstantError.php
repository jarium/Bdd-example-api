<?php

namespace app\Api;

class ConstantError
{
    //Bad Request Errors
    public const PARAM_ERROR_INVALID_REQUEST = '400000';
    public const PARAM_ERROR_INVALID_TITLE = '400001';
    public const PARAM_ERROR_INVALID_CONTENT = '400002';
    public const PARAM_ERROR_INVALID_CATEGORY = '400003';
    public const PARAM_ERROR_INVALID_JSON = '400004';

    //Unauthorized Errors
    public const UNAUTHORIZED_ERROR = '401000';
    public const UNAUTHORIZED_REQUEST_WITHOUT_TOKEN = '401001';
    public const UNAUTHORIZED_INVALID_TOKEN = '401002';
    public const UNAUTHORIZED_PASSIVE_TOKEN = '401003';
    public const UNAUTHORIZED_EXPIRED_TOKEN = '401004';
    public const UNAUTHORIZED_TOKEN_TIME_EXPIRED = '401005';

    //Not Found Errors
    public const NOT_FOUND_ERROR = '404000';
    public const NOT_FOUND_ROUTE = '404001';

    //Internal Errors
    public const INTERNAL_ERROR = '500000';
    public const INTERNAL_MAINTENANCE_ERROR = '500001';
}