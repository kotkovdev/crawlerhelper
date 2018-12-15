<?php
namespace App\Controllers;

class SaveController
{
    public function __invoke($req, $res)
    {
        global $container;
        $container->view->render($res, 'download_and_save.twig');
    }
}