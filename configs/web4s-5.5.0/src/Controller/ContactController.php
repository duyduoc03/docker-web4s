<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Google;
use Cake\Http\Client;

class ContactController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function sendInfo() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];    
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('Contact')->sendInfo($data);
        $this->responseJson($result);
    }

}