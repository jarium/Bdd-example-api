<?php

namespace app\controllers;

use app\Api\Request;
use app\Api\Response;
use app\models\Posts;

class ApiController
{
    public static function createPosts(Request $request): void
    {
        $post = new Posts();
        $post->adminId = $request->apiToken->adminId;
        $post->loadByPostData($request->body)->save();

        Response::sendSuccessResponse();
    }
}