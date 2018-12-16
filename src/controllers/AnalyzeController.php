<?php
namespace App\Controllers;

use App\Controllers\Controller;

class AnalyzeController extends Controller
{
    public function __invoke($req, $res)
    {
        global $container;
        $data = (array)$this->table->find(1);
        $data['settings'] = json_decode($data['settings']);
        $container->view->render($res, 'download_and_analyze.twig', $data);
    }

    public function save($req, $res)
    {
        $requestContent = $req->getBody()->getContents();
        if (json_decode($requestContent)) {
            $this->table->where(['id' => 1])->update(['settings' => $requestContent]);
            echo json_encode(['result' => 'true']);
        } else {
            echo json_encode(['result' => 'false']);
        }
    }
}