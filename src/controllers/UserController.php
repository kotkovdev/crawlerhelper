<?php
namespace App\Controllers;

use App\Controllers\Controller;

class UserController
{
    public function __invoke($req, $res)
    {
        global $container;
        $container->view->render($res, 'login.twig');
    }

    public function forgot($req, $res)
    {
        global $container;
        $container->view->render($res, 'forgot.twig');
    }

    public function login($req, $res)
    {
        var_dump($req->getParsedBody());
    }
}