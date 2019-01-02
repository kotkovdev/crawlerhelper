<?php
namespace App\Controllers;

use App\Models\Queue as Queue;
use App\Models\Instance as Instance;

class JobsController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = [];
        $jobs = new Queue;
        $jobs = $jobs::orderBy('id', 'DESC')->get()->toArray();
        foreach ($jobs as &$job) {
            if ($job['instance_id'] > 0) {
                $job['instance'] = Instance::find($job['instance_id'])->toArray();
                $job['settings'] = json_decode($job['settings'], true);
            }
        }
        $data['jobs'] = $jobs;
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

    public function log($req, $res)
    {
        $name = $_GET['name'];
        $filepath = LOG_PATG . DIRECTORY_SEPARATOR . $name . '.log';
        $content = file_get_contents($filepath);
        echo $content;
    }
}