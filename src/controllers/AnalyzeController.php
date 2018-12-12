<?php
namespace App\Controllers;

class AnalyzeController {
    public function __invoke($req, $res) {
        global $container;


        $container->view->render($res, 'download_and_analyze.twig');
    }
}