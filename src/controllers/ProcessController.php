<?php
namespace App\Controllers;

use Illuminate\Database\Query\Builder;
use App\Classes\WGET as WGET;
use App\Models\Queue as Queue;
use App\Models\Instance as Instance;

class ProcessController
{
    protected $jobs;
    protected $instances;

    public function __construct(Builder $jobs, Builder $instances)
    {
        $this->jobs = $jobs;
        $this->instances = $instances;
    }

    /**
     * Explode urls and add jobs to queue
     *
     * @param $req
     * @param $res
     */
    public function __invoke($req, $res)
    {
        $data = $req->getBody()->getContents();
        $settings = json_decode($data, true);
        $settings['urls'] = trim($settings['urls'], " \t\n\r");
        $urls = explode(PHP_EOL, $settings['urls']);
        $type = $settings['type'];
        unset($settings['type']);
        unset($settings['urls']);
        foreach ($urls as $url) {
            $url = trim($url, " \t\n\r");
            $queue = new Queue;
            $queue->url = $url;
            $queue->type = (int)$type;
            $queue->settings = $data;
            $queue->status = 1;
            $queue->save();
        }
    }

    public function run()
    {
        $result = shell_exec( '/usr/local/bin/php ' . $_SERVER['DOCUMENT_ROOT'] . '/../crawler.php  > /dev/null &');
        echo json_encode(['status' => 'done', 'result' => $result]);
    }

    /**
     *
     * Crawler worker
     *
     * @param int $limit
     */
    public function process($limit = 10)
    {
        Queue::chunk($limit, function($jobs) {
            foreach ($jobs as $job) {
                $instncesPath = PUBLIC_DIR . '/upload/instances/';
                if ($job->status !== 3) {
                    $job->status = 2;
                    $job->save();
                    $settings = json_decode($job->settings, true);
                    $settings['urls'] = $job->url;
                    $wget = new WGET($settings);
                    $wget->setPath($instncesPath);
                    if ($job->type == 2) {
                        $wget->setRecursive(true);
                    }
                    /**
                     * Saving instance info for live log watching
                     */
                    $instance = new Instance;
                    $instance->url = $job->url;
                    $instance->name = $wget->getName($job->url);
                    $instance->is_exists = 1;
                    $instance->path = " ";
                    $instance->save();
                    $job->instance_id = $instance->id;
                    $job->save();
                    /**
                     * Run processing
                     */
                    $result = $wget->process();
                    /**
                     * Update information of instances
                     */
                    $instance->path = $instncesPath . $result[0]['path'];
                    $instance->name = $result[0]['name'];
                    $instance->save();
                    $job->instance_id = $instance->id;
                    $job->status = 3;
                    $job->command = $result[0]['command'];
                    if ($job->type == 3 || $settings['function'] == 'list') {
                        //Parse resources statuses
                        $this->analyzeResources($job, $instance);
                    }
                    $job->save();
                }
            }
        });
        echo json_encode(['status' => 'done']);
    }

    /**
     * @param $job
     * @param $instance
     *
     * Parse log when get list of urls
     */
    public function analyzeResources($job, $instance)
    {
        $logPath = LOG_PATH . '/' . $instance->name . '.log';
        $log = file($logPath);
        $out = [];
        foreach ($log as $line) {
            if (strlen($line) > 10) {
                if (strpos($line, 'URL:') && !strpos($line, '->')) {
                    $out[] = explode(' ', $line);
                }
            }
        }
        $instance->path = json_encode($out);
        $instance->save();
    }
}