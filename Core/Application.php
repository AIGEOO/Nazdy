<?php

declare(strict_types=1);

namespace Core;

use Core\Config;
use Dotenv\Dotenv;
use Core\Container;
use Core\Database\Database;
use Spatie\Ignition\Ignition;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader;
use Core\Exceptions\RouteNotFoundException;

class Application
{
    private static Database $db;
    private static Container $container;
    protected ?Router $router;

    public function __construct() {
        static::$container = new Container();
        $this->router = new Router(static::$container);
    }

    public static function db(): Database
    {
        return static::$db;
    }

    public static function container(): Container
    {
        return static::$container;
    }

    public function boot(): self
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        
        static::$container->set(Config::class, fn() => new Config($_ENV));
        
        static::$db = new Database(static::$container->get(Config::class)->db ?? []);
        
        static::$container->set(Twig::class, function() 
        {
            return new Twig(
                new FilesystemLoader(VIEW_PATH),
                [
                    'cache' => STORAGE_PATH . '/cache',
                    'auto_reload' => true
                ]
            );
        });

        static::$container->set(Mailing::class, fn() => new Mailing(static::$container->get(Config::class)->mailer['dsn']));

        Ignition::make()->useDarkMode()->register();

        return $this;
    }

    public function run()
    {
        if(! $this->router){
            throw new \RuntimeException("Router is not set");
        }
        
        try {
            echo $this->router->resolve();
        } catch (RouteNotFoundException $e) {
            http_response_code(404);

            echo View::make('_error', [
                'exception' => $e,
            ]);
        }
    }
} 
