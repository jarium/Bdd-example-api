<?php

namespace app;

use app\Api\ConstantError;
use app\Builder\Response;
use app\Middleware\BaseMiddleware;
use app\models\Common\Request;
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

    public function __construct()
    {
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

    public function get($url, $fn, $tokenRequired = true)
    {
        $this->getRoutes[$url] = $fn;
        $this->tokenRequiredByRoutes[$url] = $tokenRequired;
    }

    public function post($url, $fn, $tokenRequired = true)
    {
        $this->postRoutes[$url] = $fn;
        $this->tokenRequiredByRoutes[$url] = $tokenRequired;
    }

    public function resolve()
    {
        if (MAINTENANCE) {
            new BaseMiddleware(false); //Base middleware logs the request

            (new Response())
                ->setHttpStatusCode(503)
                ->setErrorCode(ConstantError::INTERNAL_MAINTENANCE_ERROR)
                ->setErrorMessage('maintenance, please visit us later')
                ->send();
        }

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

            (new Response())
                ->setHttpStatusCode(404)
                ->setErrorCode(ConstantError::NOT_FOUND_ROUTE)
                ->setErrorMessage('route not found!')
                ->send();
        }
    }
}