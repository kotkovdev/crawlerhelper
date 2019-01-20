<?php
namespace App\Controllers;

use App\Models\Instance as Instance;

class InstancesController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = [];
        $data['instances'] = [];
        $instances = Instance::orderBy('id', 'DESC')->get()->toArray();
        foreach ($instances as $instance) {
            $job = \App\Models\Queue::where('instance_id', '=', $instance['id'])->first();
            if ($job['type'] == 3) {
                $instance['instance_url'] = '/instlist/'.$instance['id'];
            } else {
                $instance['instance_url'] = '/upload/instances/' . $instance['name'];
            }
            $data['instances'][] = $instance;
        }
        $container->view->render($res, 'instances.twig', $data);
    }

    public function remove($req, $res)
    {
        $instanceId = $_POST['id'];
        $instance = Instance::find($instanceId);
        $instanceFields = $instance->toArray();
        $target = $instanceFields['path'];
        $this->delete_files($target);
        $instance->is_exists = 0;
        $instance->save();
        unlink(LOG_PATH . DIRECTORY_SEPARATOR . $instanceFields['name'] . '.log');
        echo json_encode(['result' => true]);

    }

    function delete_files($target)
    {
        if ($target) {
            echo json_encode(['result' => false]);
        }
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

            foreach( $files as $file ){
                $this->delete_files( $file );
            }

            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }

    public function list($req, $res, $args)
    {
        global $container;
        $instance = Instance::find($args['id'])->toArray();
        $data['instance'] = $instance;
        $data['resources'] = json_decode($instance['path']);
        $container->view->render($res, 'instance.twig', $data);
    }
}