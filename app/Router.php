<?php

namespace app;

use app\Api\ConstantError;
use app\Api\Request;
use app\Exception\BadRequestException;
use app\Exception\NotFoundException;
use app\Exception\UnauthorizedException;
use app\Middleware\BaseMiddleware;
use ReflectionClass;
use ReflectionException;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public array $tokenRequiredByRoutes = [];
    public array $middlewares = [];

    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws ReflectionException
     */
    public function middleware(string $middlewareClassName): Router
    {
        $reflectionOfClass = new ReflectionClass($middlewareClassName);
        $this->middlewares[$reflectionOfClass->getShortName()] = $middlewareClassName;

        return $this;
    }

    public function get($url, $fn, $tokenRequired = true): void
    {
        $this->getRoutes[$url] = $fn;
        $this->tokenRequiredByRoutes[$url] = $tokenRequired;
    }

    public function post($url, $fn, $tokenRequired = true): void
    {
        $this->postRoutes[$url] = $fn;
        $this->tokenRequiredByRoutes[$url] = $tokenRequired;
    }

    /**
     * @throws NotFoundException|BadRequestException
     * @throws UnauthorizedException
     */
    public function resolve(): void
    {
        if (strpos($this->request->requestUri, '?') !== false) {
            $this->request->requestUri = substr($this->request->requestUri, 0, strpos($this->request->requestUri, '?'));
        }

        if ($this->request->requestMethod === 'GET') {
            $fn = $this->getRoutes[$this->request->requestUri] ?? null;
        } else {
            $fn = $this->postRoutes[$this->request->requestUri] ?? null;
        }

        if ($fn) {
            $isApiTokenRequired = $this->tokenRequiredByRoutes[$this->request->requestUri];
            $middlewareClassName = $this->middlewares[ucfirst($fn[1]) . 'Middleware'] ?? null;

            if ($middlewareClassName) {
                $middleware = new $middlewareClassName($isApiTokenRequired, $this->request);
                $middleware->handle();
                $this->request = $middleware->request;
            } else {
                $this->request = (new BaseMiddleware($isApiTokenRequired, $this->request))->request;
            }

            call_user_func($fn, $this->request);
        } else {
            throw new NotFoundException(
                'route not found!',
                ConstantError::NOT_FOUND_ROUTE
            );
        }
    }
}