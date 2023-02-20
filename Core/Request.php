<?php

declare(strict_types=1);

namespace Core;

use Core\Validator;

class Request extends Validator
{
    public array $params = [];

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $route = explode('?', $uri)[0];

        return $route;
    }

    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtolower($method);
    }

    public function setRouteParams(array $params) {
        $this->params = $params;
    }

    public function getBody(): array
    {
        $data = [];
        if ($this->isMethod('GET')) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isMethod('POST')) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }
}
