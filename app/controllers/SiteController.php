<?php

namespace App\Controllers;

use Core\View;
use Core\Mailing;
use Core\Request;
use Core\Response;
use Core\Container;
use Core\Application;
use Symfony\Component\Mime\Email;

class SiteController
{
    public function __construct(public Request $request, public Response $response)
    {
        
    }
    public function home()
    {
        return View::make(
            view: 'home',
            params: [
                'name' => 'AIGEO_',
                'pageTitle' => 'Nazdy | Home'
            ],
            layout:'main');
    }

    public function contact()
    {
        // $text = <<<Body
        //     Hello AIGEO_

        //     Thank you for visiting contact page!
        // Body;

        // $email = (new Email())
        //     ->from('support@example.com')
        //     ->to('yosifsalimalessaei@gmail.com')
        //     ->subject('Welcom')
        //     ->text($text);

        // (new Mailing())->send($email);
        
        return View::make(view: 'contact', layout: 'main');
    }
}
