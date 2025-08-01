<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class LadiPageController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function webhooksLadipage() {
        $this->layout = false;
        $this->autoRender = false;

        $data_bearer = $this->data_bearer;
        // ladipage_secret_key
        if (empty($data_bearer['ladipage']) || $data_bearer['ladipage'] != 1) {
            return $this->responseJson([
                STATUS => 400,
                MESSAGE => __d('template', 'Secret Key for authentication is incorrect.')
            ]);
        }

        $data = $this->request->input('json_decode', true);

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Ladipage')->sniffRequests($data);    

        // Log::write('debug', json_encode($data));
        exit(json_encode($result));
    }
}