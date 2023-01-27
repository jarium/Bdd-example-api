<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use app\Api\Response;
use app\controllers\ApiController;
use app\Exception\ApiException;
use app\Middleware\CreatePostsMiddleware;
use app\Router;

try {
    define('REQUEST_ID', uniqid('-REQ-', true));

    $router = new Router();

    $router
        ->middleware(CreatePostsMiddleware::class)
        ->post('/api/create-post', [ApiController::class, 'createPosts']);

    $router->resolve();
} catch (ApiException $e) {
    Response::sendErrorResponse($e);
} catch (Throwable $t) {
    Response::sendInternalErrorResponse($t);
}
