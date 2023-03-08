<?php

declare(strict_types=1);

namespace Core;

use Core\Config;
use Core\Logger;
use Core\Request;
use Core\Response;
use Core\Container;
use Core\Application;
use Core\Exceptions\RouteNotFoundException;
use Core\Exceptions\MiddlewareNotFoundException;

class Router
{
    private static array $routes = [];
    private array $middlewares = [];
    private Request $request;
    private Response $response;

    public function __construct() {
        $this->request   = new Request();
        $this->response  = new Response();
    }

    public function middleware(array $middlewares)
    {
        $config = Application::container()->get(Config::class)->middlewares;

        foreach ($middlewares as $middleware) {
            if (! $config[$middleware]) {
                Logger::error("Middleware: Middleware Not Found");

                throw new MiddlewareNotFoundException();
            }

            array_push($this->middlewares, $config[$middleware]);
        }

        return $this;
    }

    protected function register(string $method, string $route, callable|array $callback): self
    {
        if (! empty($this->middlewares)) {
            static::$routes[$method][$route] = [
                'middlewares' => $this->middlewares,
                'callback' => $callback,
            ];

        } else {
            static::$routes[$method][$route] = $callback;
        }
        
        return $this;
    }

    public function get(string $route, callable|array $callback): self
    {
        return $this->register('get', $route, $callback);
    }

    public function post(string $route, callable|array $callback): self
    {
        return $this->register('post', $route, $callback);
    }

    public function put(string $route, callable|array $callback): self
    {
        return $this->register('put', $route, $callback);
    }

    public function delete(string $route, callable|array $callback): self
    {
        return $this->register('delete', $route, $callback);
    }

    function redirect(string $from, string $to, int $code = 301): void
    {
        $this->response->redirect($from, $to);
    }

    public function resolve()
    {
        $method = $this->request->getMethod();
        $route = $this->request->getUri();
        $routeInfo = static::$routes[$method][$route] ?? null;
        
        if (! $routeInfo) {
            Logger::error("Router: Route Not Found");

            throw new RouteNotFoundException();
        }

        $middleware = $routeInfo['middlewares'] ?? [];
        $callback = $routeInfo['callback'] ?? $routeInfo;

        foreach ($middleware as $class) {
            if (class_exists($class)) {
                $class = Application::container()->get($class);
                
                if(method_exists($class, 'handle')) {
                    call_user_func([$class, 'handle'], $this->request);
                }
            }
        }

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        [$class, $method] = $callback;

        if (class_exists($class)) {

            $class = Application::container()->get($class);

            if (method_exists($class, $method)) {
                return call_user_func([$class, $method]);
            }
        }

        Logger::error("Router: Route Not Found");

        throw new RouteNotFoundException();
    }
}