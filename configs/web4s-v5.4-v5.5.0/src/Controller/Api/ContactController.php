<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class ContactController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function formInfo()
    {
        $data = $this->data_bearer;
        
        $form_code = !empty($data['form_code']) ? $data['form_code'] : '';
        if(empty($form_code)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'ma_form_duoc_cau_hinh_khong_ton_tai_tren_he_thong')
            ]);
        }

        $form_info = TableRegistry::get('ContactsForm')->find()->where([
            'code' => $form_code,
            'deleted' => 0
        ])->select(['id','name', 'code', 'fields'])->first();

        if(empty($form_info)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'ma_form_duoc_cau_hinh_khong_ton_tai_tren_he_thong')
            ]);
        } 
        
        $form_info['fields'] = !empty($form_info['fields']) ? json_decode($form_info['fields']) : [];

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $form_info
        ]);
    }

    public function sendInfo()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Contact')->sendInfo($data);
        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){      
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

}

