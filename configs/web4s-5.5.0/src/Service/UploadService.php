<?php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;

class UploadService extends AppService
{
    
    public function upload($id = null, $data = [], $lang = null)
    {   
        if (empty($id) || empty($data) || empty($lang)) {
            $this->responseData([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        
        
        
    }
}
