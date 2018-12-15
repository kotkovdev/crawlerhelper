<?php
namespace App\Controllers;

use App\Controllers\Controller;

class AnalyzeController extends Controller{
    public function __invoke($req, $res) {
        global $container;
        $container->view->render($res, 'download_and_analyze.twig');
    }
}