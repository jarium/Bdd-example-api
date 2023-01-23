<?php

namespace app\Middleware;

interface MiddlewareInterface
{
    public function handle(): void;
}