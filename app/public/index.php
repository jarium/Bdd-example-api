<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use app\controllers\ApiController;
use app\Middleware\CreatePostsMiddleware;
use app\Router;

define ('REQUEST_ID', uniqid('-REQ-', true));

$router = new Router();

$router
    ->middleware(CreatePostsMiddleware::class)
    ->post('/api/create-post', [ApiController::class, 'createPosts']);

$router->resolve();
