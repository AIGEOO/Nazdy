<?php

declare(strict_types = 1);

namespace Core;

class Config
{
    protected array $config = [];

    public function __construct(array $env)
    {
        $this->config = [
            'db' => [
                'host'     => $env['DB_HOST'],
                'port'     => $env['DB_PORT'],
                'user'     => $env['DB_USER'],
                'pass'     => $env['DB_PASS'],
                'name'     => $env['DB_NAME'],
                'driver'   => $env['DB_DRIVER'] ?? 'mysql',
            ],
            'app' => [
                'name'     => $env['APP_NAME'],
                'environment' => $env['APP_ENVIRONMENT'] ?? 'production',
            ],
            'middlewares' => [
                'auth' => "Core\Middlewares\AuthMiddleware",
                'authorize' => "Core\Middlewares\AuthorizationMiddleware",
            ],
            'mailer' => [
                'dsn' => $env['MAILER_DSN']
            ],
        ];
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}