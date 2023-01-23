<?php

namespace app\Middleware;

use app\Api\ConstantError;
use app\Builder\Response;
use app\helpers\TimeHelper;
use app\Logger\Logger;
use app\models\ApiToken;
use app\models\Common\Request;

class BaseMiddleware
{
    public Request $request;

    public function __construct(bool $tokenRequired)
    {
        $headers = getallheaders();

        if (!is_array($headers)) {
            $headers = [$headers];
        }

        $body = $this->getJsonBodyAsArray();

        $this->request = new Request($headers, $body ?? []);
        $this->logRequest();

        if ($body === null) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_JSON)
                ->setErrorMessage('Invalid json')
                ->send();
        }

        $this->trimBodyData();

        if (!$tokenRequired) {
            return;
        }

        $token = $this->checkToken();
        $this->request->apiToken = $token;
    }

    private function logRequest(): void
    {
        $log = REQUEST_ID . " Request: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'] . ", ";
        $log .= "Headers: ";
        foreach ($this->request->headers as $header => $value) {
            $log .= "$header: $value, ";
        }
        $log .= "Body: ";
        foreach ($this->request->body as $param => $value) {
            $log .= "$param: $value, ";
        }

        (new Logger())->log($log, 'INFO');
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

    private function checkToken(): ApiToken
    {
        $accessKey = $this->request->headers['X-Posts-Token'] ?? null;

        if (!$accessKey) {
            (new Response())
                ->setHttpStatusCode(401)
                ->setErrorCode(ConstantError::UNAUTHORIZED_REQUEST_WITHOUT_TOKEN)
                ->setErrorMessage('Invalid token parameter')
                ->send();
        }

        $token = ApiToken::findByToken($accessKey);

        if (!$token) {
            (new Response())
                ->setHttpStatusCode(401)
                ->setErrorCode(ConstantError::UNAUTHORIZED_INVALID_TOKEN)
                ->setErrorMessage('Token not found!')
                ->send();
        }

        if ($token->status === ApiToken::STATUS_PASSIVE) {
            (new Response())
                ->setHttpStatusCode(401)
                ->setErrorCode(ConstantError::UNAUTHORIZED_PASSIVE_TOKEN)
                ->setErrorMessage('Invalid token parameter')
                ->send();
        }

        if ($token->status === ApiToken::STATUS_EXPIRED) {
            (new Response())
                ->setHttpStatusCode(401)
                ->setErrorCode(ConstantError::UNAUTHORIZED_EXPIRED_TOKEN)
                ->setErrorMessage('Token expired!')
                ->send();
        }

        if ($token->expiredAt && TimeHelper::now() > $token->expiredAt) {
            $token->status = ApiToken::STATUS_EXPIRED;
            $token->save();

            (new Response())
                ->setHttpStatusCode(401)
                ->setErrorCode(ConstantError::UNAUTHORIZED_TOKEN_TIME_EXPIRED)
                ->setErrorMessage('Token expired!')
                ->send();
        }

        return $token;
    }

    private function trimBodyData(): void
    {
        array_walk_recursive($this->request->body, static function (&$param) {
            $param = is_string($param) ? trim($param) : $param;
        });
    }
}