<?php

declare(strict_types=1);

namespace Core\Database;

require '/../../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Core\Application;

class Migrator
{
    public function __construct()
    {
        $dbConfigs = Application::container()->get(Config::class)->db;
        $config = new PhpFile(CONFIG_PATH . '/migrations.php'); 

        $conn = DriverManager::getConnection([
            'dbname' => $dbConfigs['name'],
            'user' => $dbConfigs['user'],
            'password' => $dbConfigs['password'],
            'host' => $dbConfigs['host'],
            'driver' => 'pdo_mysql',
        ]);

        return DependencyFactory::fromConnection($config, new ExistingConnection($conn));
    }
}