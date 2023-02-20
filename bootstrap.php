<?php

use Core\Application;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Core/Config/path_constants.php';

$app = new Application();

$app->boot();

require_once __DIR__ . '/routes/web.php';

return $app;