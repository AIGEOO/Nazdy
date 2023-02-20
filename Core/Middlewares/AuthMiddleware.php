<?php

declare(strict_types=1);

namespace Core\Middlewares;

use Core\Request;
use Core\Contracts\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    
    public function handle(Request $request): void
    {
        if (true) {
            throw new \Exception("Error Processing Request");
        }
    }
}