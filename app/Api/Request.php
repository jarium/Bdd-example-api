<?php

namespace app\Api;

use app\Exception\MaintenanceException;
use app\Logger\Logger;
use app\models\ApiToken;

class Request
{
    public array $headers = [];
    public ?array $body = [];

    public string $requestUri;

    public string $requestMethod;

    public ?ApiToken $apiToken = null;

    /**
     * @throws MaintenanceException
     */
    public function __construct()
    {
        define('REQUEST_ID', uniqid('-REQ-', true));

        $headers = getallheaders();

        if (!is_array($headers)) {
            $headers = [$headers];
        }

        $this->headers = $headers;
        $this->body = $this->getJsonBodyAsArray();
        $this->requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];

        $this->logRequest();

        if (MAINTENANCE) {
            throw new MaintenanceException();
        }
    }

    private function getJsonBodyAsArray(): ?array
    {
        $json = file_get_contents('php://input');

        if (!$json) {
            return [];
        }

        $data = json_decode($json, true);

        if (!$data || json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }

    private function logRequest(): void
    {
        $log = REQUEST_ID . " Request: " . $this->requestMethod . " " . $this->requestUri . ", ";
        $log .= "Headers: ";

        foreach ($this->headers as $header => $value) {
            $log .= "$header: $value, ";
        }

        $log .= "Body: ";

        if ($this->body !== null) {
            $log .= json_encode($this->body);
        }

        (new Logger())->log($log, 'INFO');
    }
}