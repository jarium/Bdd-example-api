<?php

namespace app\models\Common;

use app\models\ApiToken;

class Request
{
    public array $headers = [];
    public array $body = [];
    public ?ApiToken $apiToken = null;

    public function __construct(array $headers, array $body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }
}