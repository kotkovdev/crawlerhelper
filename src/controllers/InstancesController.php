<?php
namespace App\Controllers;

use App\Models\Instance as Instance;

class InstancesController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = [];
        $instances = new Instance;
        $data['instances'] = $instances::orderBy('id', 'DESC')->get()->toArray();
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
}