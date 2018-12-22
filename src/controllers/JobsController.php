<?php
namespace App\Controllers;

use App\Models\Jobs as Jobs;

class JobsController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = [];
        $jobs = new Jobs;
        $data['jobs'] = $jobs::orderBy('id', 'DESC')->get()->toArray();
        $container->view->render($res, 'jobs.twig', $data);
    }
}