<?php

namespace app\Api;

use app\Exception\ApiException;
use app\helpers\LoggerHelper;
use app\Logger\Logger;
use Throwable;

class Response
{
    private string $httpStatusCode;
    private bool $success = false;
    private ?array $data = null;
    private ?string $errorCode = null;
    private ?string $errorMessage = null;

    public static function sendSuccessResponse(?array $data = null): void
    {
        (new self())
            ->setHttpStatusCode(200)
            ->successResponse()
            ->setData($data)
            ->send();
    }

    public static function sendErrorResponse(ApiException $e): void
    {
        (new Logger())->log(LoggerHelper::getThrowableDetails($e), 'WARNING');

        (new self())
            ->setHttpStatusCode($e->getCode())
            ->setErrorCode($e->getCustomErrorCode())
            ->setErrorMessage($e->getMessage())
            ->send();
    }

    public static function sendInternalErrorResponse(Throwable $t): void
    {
        (new Logger())->log(LoggerHelper::getThrowableDetails($t), 'EMERGENCY');

        (new self())
            ->setHttpStatusCode(500)
            ->setErrorCode(ConstantError::INTERNAL_ERROR)
            ->setErrorMessage('Internal server error')
            ->send();
    }

    /**
     * @return string
     */
    public function getHttpStatusCode(): string
    {
        return $this->httpStatusCode;
    }

    /**
     * @param string $httpStatusCode
     * @return Response
     */
    public function setHttpStatusCode(string $httpStatusCode): Response
    {
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return Response
     */
    public function setData(?array $data): Response
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return Response
     */
    public function successResponse(): Response
    {
        $this->success = true;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @param string|null $errorCode
     * @return Response
     */
    public function setErrorCode(?string $errorCode): Response
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     * @return Response
     */
    public function setErrorMessage(?string $errorMessage): Response
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function send(): void
    {
        $response = [
            'meta' => [
                'requestId' => REQUEST_ID,
                'httpStatusCode' => $this->getHttpStatusCode(),
            ]
        ];

        if ($this->isSuccess()) {
            $response['meta']['success'] = true;
            if ($this->getData()) {
                $response['meta']['data'] = $this->getData();
            }

            $this->sendResponse($response);
        }

        $response['meta']['errorCode'] = $this->getErrorCode();
        $response['meta']['errorMessage'] = $this->getErrorMessage();

        $this->sendResponse($response);
    }

    private function sendResponse(array $response): void
    {
        $this->logResponse($response);

        http_response_code($response['meta']['httpStatusCode']);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }

    private function logResponse(array $response): void
    {
        (new Logger())->log(REQUEST_ID . ' Response: ' . json_encode($response), 'INFO');
    }
}
