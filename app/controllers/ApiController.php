<?php

namespace app\controllers;

use app\Builder\Response;
use app\models\Posts;
use app\Router;

class ApiController
{
    public static function createPosts(Router $router): void
    {
        $post = new Posts();
        $post->adminId = $router->request->apiToken->adminId;
        $post->loadByPostData($router->request->body)->save();

        (new Response())
            ->setHttpStatusCode(200)
            ->successResponse()
            ->send();
    }
}