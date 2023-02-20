<?php

use Core\Router;
use App\Controllers\SiteController;
use App\Controllers\AboutController;

$router = new Router();

$router->redirect('/', '/home');
$router->get('/home', [SiteController::class, 'home']);
$router->get('/contact', [SiteController::class, 'contact']);
$router->get('/about', [AboutController::class, 'index']);
$router->middleware(['auth'])->get('/profile', [AboutController::class, 'index']);
