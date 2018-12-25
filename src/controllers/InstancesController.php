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
        $data = $req->getBody()->getContents();
    }
}