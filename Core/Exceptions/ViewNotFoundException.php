<?php

namespace Core\Exceptions;


class ViewNotFoundException extends \Exception
{
    protected $message = 'View Not Found';
    protected $code = 403;
}