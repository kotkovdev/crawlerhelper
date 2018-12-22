<?php
namespace App\Controllers;

use Illuminate\Database\Query\Builder;
use App\Classes\WGET as WGET;
use App\Models\Jobs as Jobs;

class ProcessController
{
    protected $jobs;
    protected $instances;

    public function __construct(Builder $jobs, Builder $instances)
    {
        $this->jobs = $jobs;
        $this->instances = $instances;
    }

    public function __invoke($req, $res)
    {
        $data = $req->getBody()->getContents();
        $settings = json_decode($data);
        $wget = new WGET($settings);
        $wget->setPath(PUBLIC_DIR . '/upload/instances/');
        $outputs = $wget->process();
        foreach ($outputs as $output) {
            $job = new Jobs;
            $job->url = $output['url'];
            $job->type = 1;
            $job->settings = $data;
            $job->status = $output['status'];
            $job->save();
        }
    }

    public function setSettings($settings) {

    }
}