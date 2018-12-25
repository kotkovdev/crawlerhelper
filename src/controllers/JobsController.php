<?php
namespace App\Controllers;

use App\Models\Queue as Queue;

class JobsController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = [];
        $jobs = new Queue;
        $data['jobs'] = $jobs::orderBy('id', 'DESC')->get()->toArray();
        $container->view->render($res, 'jobs.twig', $data);
    }

    public function remove($req, $res)
    {
        $data = $req->getBody()->getContents();
        $data = json_decode($data);
        $queue = new Queue;
        $job = $queue->find($data->id);
        $job->delete();
    }
}