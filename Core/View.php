<?php

declare(strict_types=1);

namespace Core;

use Core\Config;
use Core\Logger;
use Core\Application;
use Twig\Environment as Twig;
use Core\Exceptions\ViewNotFoundException;

class View
{
    protected Twig $twig;

    public function __construct(
        protected string $view,
        protected ?array $params,
        protected ?string $layout
    ) {
        $this->twig = Application::container()->get(Twig::class);
    }

    public static function make(
        string $view,
        array $params = [],
        string $layout = null,
    ): static
    {
        return new static($view, $params, $layout);
    }

    public function render(): string
    {
        $layout = "/layouts/" . $this->layout . ".html.twig";
        $view = $this->view . ".html.twig";

        $title = $this->params['pageTitle'] ?? Application::container()->get(Config::class)->app['name'];
        $parameters = $this->params;
        
        try {
            if ($this->layout) {
                return $this->twig->render($layout, array_merge([
                    'content' => $view,
                    'title' => $title
                ], $parameters));
            }
    
            return $this->twig->render($view, $parameters);

        } catch (\Throwable $th) {
            Logger::error("View: " . $th->getMessage());
            
            throw new ViewNotFoundException();
        }
    }

    public function __toString(): string
    {
        return $this->render();
    }
}