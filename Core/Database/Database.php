<?php

declare(strict_types = 1);

namespace Core\Database;

use PDO;

/**
 * @mixin PDO
 */
class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $defaultOptions = [
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        try {
            $driver = $config['driver'];
            $host   = $config['host'];
            $port   = $config['port'];
            $name   = $config['name'];
            $dsn    = "$driver:host=$host;port=$port;dbname=$name";

            $this->pdo = new PDO(
                $dsn,
                $config['user'],
                $config['pass'],
                $config['options'] ?? $defaultOptions
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function lol() {
        var_dump($this);
    }
}