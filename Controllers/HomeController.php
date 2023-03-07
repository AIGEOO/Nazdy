<?php

namespace App\Controllers;

use Core\View;
use Core\Request;
use Core\Response;

class HomeController
{
    public function __construct (
        public Request $request,
        public Response $response
    ) {}

    public function index()
    {
        return View::make(
            view: 'home',
            params: [
                'name' => "AIGEO_",
                'pageTitle' => 'Nazdy | Home'
            ],
            layout:'main');
    }

    public function store()
    {   
        return View::make(view: 'contact', layout: 'main');
    }

    public function show()
    {
        //
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
