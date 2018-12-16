<?php
namespace App\Controllers;

use Illuminate\Database\Query\Builder;
use App\Classes\WGET as WGET;

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
        while ($wget->process()) {

        }

    }

    public function setSettings($settings) {

    }
}