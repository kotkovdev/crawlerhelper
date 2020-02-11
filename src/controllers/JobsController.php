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
                if ($job['type'] == 3 || $job['settings']['function'] == 'list') {
                    $job['instance_url'] = '/instlist/' . $job['instance']['id'];
                } else {
                    $job['instance_url'] = '/upload/instances/' . $job['instance']['name'];
                }

            }
        }
        $data['jobs'] = $jobs;
        $data['lock'] = $this->checkLock();
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
        try {
            $name = $_GET['name'];
            $filepath = LOG_PATH . DIRECTORY_SEPARATOR . $name . '.log';
            if (file_exists($filepath)) {
                $content = file_get_contents($filepath);
            } else {
                throw new \Exception('Can not find log file');
            }

        } catch (\Exception $e) {
            die('Can not find log file in path : ' . $filepath . 'Error code:' . $e->GetCode());
        }
        echo nl2br($content);
    }
}