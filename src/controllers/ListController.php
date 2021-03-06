<?php
namespace App\Controllers;

use App\Models\Settings as Settings;
use Illuminate\Database\Query\Builder;

class ListController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = (array)$this->table->find(3);
        $data['settings'] = json_decode($data['settings']);
        $data['lock'] = $this->checkLock();
        $container->view->render($res, 'list_resources.twig', $data);
    }

    public function save($req, $res)
    {
        $requestContent = $req->getBody()->getContents();
        $settings = Settings::find(3);
        if (is_null($settings)) {
            $settings = new Settings;
        }
        $settings->id = 3;
        $settings->settings = $requestContent;
        $settings->save();
        echo json_encode(['result' => true]);
    }
}