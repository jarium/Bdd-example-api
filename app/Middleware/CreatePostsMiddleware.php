<?php

namespace app\Middleware;

use app\Api\ConstantError;
use app\Builder\Response;
use app\helpers\CategoryHelper;

class CreatePostsMiddleware extends BaseMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $this->checkTitle();
        $this->checkContent();
        $this->checkCategory();
    }

    private function checkTitle(): void
    {
        if (!isset($this->request->body['title']) || !$this->request->body['title']) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_TITLE)
                ->setErrorMessage('title param is required')
                ->send();
        }

        if (!is_string($this->request->body['title'])) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_TITLE)
                ->setErrorMessage('title param must be a string')
                ->send();
        }

        if (strlen($this->request->body['title']) > 256) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_TITLE)
                ->setErrorMessage('title param cannot be longer than 256 characters')
                ->send();
        }
    }

    private function checkContent()
    {
        if (!isset($this->request->body['content']) || !$this->request->body['content']) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CONTENT)
                ->setErrorMessage('content param is required')
                ->send();
        }

        if (!is_string($this->request->body['content'])) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CONTENT)
                ->setErrorMessage('content param must be a string')
                ->send();
        }

        if (strlen($this->request->body['content']) > 2048) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CONTENT)
                ->setErrorMessage('content param cannot be longer than 2048 characters')
                ->send();
        }
    }

    private function checkCategory()
    {
        if (!isset($this->request->body['category']) || !$this->request->body['category']) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CATEGORY)
                ->setErrorMessage('category param is required')
                ->send();
        }

        if (!is_string($this->request->body['category'])) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CATEGORY)
                ->setErrorMessage('category param must be a string')
                ->send();
        }

        if (strlen($this->request->body['category']) > 64) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CATEGORY)
                ->setErrorMessage('category param cannot be longer than 64 characters')
                ->send();
        }

        $availableCategories = CategoryHelper::getAvailableCategories();

        if (!in_array($this->request->body['category'], $availableCategories, true)) {
            (new Response())
                ->setHttpStatusCode(400)
                ->setErrorCode(ConstantError::PARAM_ERROR_INVALID_CATEGORY)
                ->setErrorMessage('category param must be one of the following: ' . implode(', ', $availableCategories))
                ->send();
        }
    }
}