<?php

declare(strict_types=1);

namespace Core;

use Core\Request;

class Response
{
    public int $statusCode;
    public Request $request;

    public function __construct()
    {
        $this->request = new Request();    
    }

    public function setStatusCode(int $code): int
    {
        http_response_code($code);
        $this->statusCode = $code;

        return $this->statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function redirect(string $from, string $to, int $code = 301) {
        if ($from === $this->request->getUri()) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $to");
            die();
        }
    }
    
    public function redirectTo(string $to, int $code = 301): void
    {
        http_response_code($code);
        header("Location: {$to}");
        die();
    } 
}