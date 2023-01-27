<?php

namespace app\Middleware;

use app\Api\ConstantError;
use app\Api\Request;
use app\Exception\BadRequestException;
use app\Exception\UnauthorizedException;
use app\helpers\TimeHelper;
use app\Logger\Logger;
use app\models\ApiToken;

class BaseMiddleware
{
    public Request $request;

    /**
     * @throws BadRequestException
     */
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
            throw new BadRequestException(
                'Invalid json',
                ConstantError::PARAM_ERROR_INVALID_JSON
            );
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

    /**
     * @throws UnauthorizedException
     */
    private function checkToken(): ApiToken
    {
        $accessKey = $this->request->headers['X-Posts-Token'] ?? null;

        if (!$accessKey) {
            throw new UnauthorizedException(
                'Invalid token parameter',
                ConstantError::UNAUTHORIZED_REQUEST_WITHOUT_TOKEN
            );
        }

        $token = ApiToken::findByToken($accessKey);

        if (!$token) {
            throw new UnauthorizedException(
                'Token not found!',
                ConstantError::UNAUTHORIZED_INVALID_TOKEN
            );
        }

        if ($token->status === ApiToken::STATUS_PASSIVE) {
            throw new UnauthorizedException(
                'Invalid token parameter',
                ConstantError::UNAUTHORIZED_PASSIVE_TOKEN
            );
        }

        if ($token->status === ApiToken::STATUS_EXPIRED) {
            throw new UnauthorizedException(
                'Token expired!',
                ConstantError::UNAUTHORIZED_EXPIRED_TOKEN
            );
        }

        if ($token->expiredAt && TimeHelper::now() > $token->expiredAt) {
            $token->status = ApiToken::STATUS_EXPIRED;
            $token->save();

            throw new UnauthorizedException(
                'Token expired!',
                ConstantError::UNAUTHORIZED_TOKEN_TIME_EXPIRED
            );
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