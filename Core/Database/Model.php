<?php

namespace Core;

use Core\Database\Database;
use Core\Database\Semiloquent;

// TODO: Add More Features

abstract class Model extends Semiloquent
{
    protected Database $db;

    public function __construct()
    {
        $table = strtolower((new \ReflectionClass(get_called_class()))->getShortName());
        parent::__construct($table);
    }
}