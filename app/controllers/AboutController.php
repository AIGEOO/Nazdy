<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class AboutController extends Controller
{
    public function index()
    {
        return View::make('about');
    }
}