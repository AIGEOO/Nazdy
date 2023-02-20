<?php

declare(strict_types = 1);

namespace Core\Exceptions;

class MiddlewareNotFoundException extends \Exception
{
    protected $message = 'Middleware not Found';
}