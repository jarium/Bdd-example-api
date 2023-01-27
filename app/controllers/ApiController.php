<?php

namespace app\controllers;

use app\Api\Response;
use app\models\Posts;
use app\Router;

class ApiController
{
    public static function createPosts(Router $router): void
    {
        $post = new Posts();
        $post->adminId = $router->request->apiToken->adminId;
        $post->loadByPostData($router->request->body)->save();

        Response::sendSuccessResponse();
    }
}