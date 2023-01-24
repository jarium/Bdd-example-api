<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use app\Api\ConstantError;
use app\Builder\Response;
use app\controllers\ApiController;
use app\helpers\LoggerHelper;
use app\Logger\Logger;
use app\Middleware\CreatePostsMiddleware;
use app\Router;

try {
    define ('REQUEST_ID', uniqid('-REQ-', true));

    $router = new Router();

    $router
        ->middleware(CreatePostsMiddleware::class)
        ->post('/api/create-post', [ApiController::class, 'createPosts']);

    $router->resolve();
} catch (Throwable $t) {
    (new Logger())->log(LoggerHelper::getThrowableDetails($t), 'EMERGENCY');
    (new Response())
        ->setHttpStatusCode(500)
        ->setErrorCode(ConstantError::INTERNAL_ERROR)
        ->setErrorMessage('Internal server error')
        ->send();
}
