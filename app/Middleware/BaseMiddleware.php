<?php

namespace app\Middleware;

use app\Api\ConstantError;
use app\Api\Request;
use app\Exception\BadRequestException;
use app\Exception\UnauthorizedException;
use app\helpers\TimeHelper;
use app\models\ApiToken;

class BaseMiddleware
{
    public Request $request;

    /**
     * @throws BadRequestException|UnauthorizedException
     */
    public function __construct(bool $tokenRequired, Request $request)
    {
        $this->request = $request;
        $this->checkRequestBody();
        $this->trimBodyData();

        if (!$tokenRequired) {
            return;
        }

        $token = $this->checkToken();
        $this->request->apiToken = $token;
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

    /**
     * @throws BadRequestException
     */
    private function checkRequestBody(): void
    {
        if ($this->request->body === null) {
            throw new BadRequestException(
                'Invalid json',
                ConstantError::PARAM_ERROR_INVALID_JSON
            );
        }
    }

    private function trimBodyData(): void
    {
        array_walk_recursive($this->request->body, static function (&$param) {
            $param = is_string($param) ? trim($param) : $param;
        });
    }
}