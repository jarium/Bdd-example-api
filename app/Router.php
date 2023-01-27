<?php

namespace app;

use app\Api\ConstantError;
use app\Api\Request;
use app\Exception\MaintenanceException;
use app\Exception\NotFoundException;
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

    public Database $db;

    /**
     * @throws MaintenanceException
     */
    public function __construct()
    {
        if (MAINTENANCE) {
            new BaseMiddleware(false); //Base middleware logs the request
            throw new MaintenanceException();
        }

        $this->db = new Database();
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
     * @throws NotFoundException
     */
    public function resolve(): void
    {
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';

        if (strpos($currentUrl, '?') !== false) {
            $currentUrl = substr($currentUrl, 0, strpos($currentUrl, '?'));
        }

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        if ($fn) {
            $isApiTokenRequired = $this->tokenRequiredByRoutes[$currentUrl];
            $middlewareClassName = $this->middlewares[ucfirst($fn[1]) . 'Middleware'] ?? null;

            if ($middlewareClassName) {
                $middleware = new $middlewareClassName($isApiTokenRequired);
                $middleware->handle();
                $this->request = $middleware->request;
            } else {
                $this->request = (new BaseMiddleware($isApiTokenRequired))->request;
            }

            call_user_func($fn, $this);
        } else {
            new BaseMiddleware(false); //Base middleware logs the request
            throw new NotFoundException(
                'route not found!',
                ConstantError::NOT_FOUND_ROUTE
            );
        }
    }
}