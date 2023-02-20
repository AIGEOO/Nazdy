<?php

declare(strict_types=1);

namespace Core\Contracts;

use Core\Request;

interface MiddlewareInterface
{
    /**
     * Handle the request
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request): void;
}
