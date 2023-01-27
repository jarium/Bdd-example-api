<?php

namespace app\Middleware;

use app\Api\ConstantError;
use app\Exception\BadRequestException;
use app\helpers\CategoryHelper;

class CreatePostsMiddleware extends BaseMiddleware implements MiddlewareInterface
{
    /**
     * @throws BadRequestException
     */
    public function handle(): void
    {
        $this->checkTitle();
        $this->checkContent();
        $this->checkCategory();
    }

    /**
     * @throws BadRequestException
     */
    private function checkTitle(): void
    {
        if (!isset($this->request->body['title']) || !$this->request->body['title']) {
            $this->invalidTitleException('title param is required');
        }

        if (!is_string($this->request->body['title'])) {
            $this->invalidTitleException('title param must be a string');
        }

        if (strlen($this->request->body['title']) > 256) {
            $this->invalidTitleException('title param cannot be longer than 256 characters');
        }
    }

    /**
     * @throws BadRequestException
     */
    private function checkContent(): void
    {
        if (!isset($this->request->body['content']) || !$this->request->body['content']) {
            $this->invalidContentException('content param is required');
        }

        if (!is_string($this->request->body['content'])) {
            $this->invalidContentException('content param must be a string');
        }

        if (strlen($this->request->body['content']) > 2048) {
            $this->invalidContentException('content param cannot be longer than 2048 characters');
        }
    }

    /**
     * @throws BadRequestException
     */
    private function checkCategory(): void
    {
        if (!isset($this->request->body['category']) || !$this->request->body['category']) {
            $this->invalidCategoryException('category param is required');
        }

        if (!is_string($this->request->body['category'])) {
            $this->invalidCategoryException('category param must be a string');
        }

        if (strlen($this->request->body['category']) > 64) {
            $this->invalidCategoryException('category param cannot be longer than 64 characters');
        }

        $availableCategories = CategoryHelper::getAvailableCategories();

        if (!in_array($this->request->body['category'], $availableCategories, true)) {
            $this->invalidCategoryException(
                'category param must be one of the following: ' . implode(', ', $availableCategories)
            );
        }
    }

    /**
     * @throws BadRequestException
     */
    private function invalidTitleException(string $errorMessage): void
    {
        throw new BadRequestException(
            $errorMessage,
            ConstantError::PARAM_ERROR_INVALID_TITLE
        );
    }

    /**
     * @throws BadRequestException
     */
    private function invalidContentException(string $errorMessage): void
    {
        throw new BadRequestException(
            $errorMessage,
            ConstantError::PARAM_ERROR_INVALID_CONTENT
        );
    }

    /**
     * @throws BadRequestException
     */
    private function invalidCategoryException(string $errorMessage): void
    {
        throw new BadRequestException(
            $errorMessage,
            ConstantError::PARAM_ERROR_INVALID_CATEGORY
        );
    }
}