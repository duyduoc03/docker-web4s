<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Google;
use Cake\Http\Client;
use Cake\Http\ServerRequest;

class ContactComponent extends Component
{
    public $components = ['System', 'Utilities', 'ReCaptcha', 'SendMessage', 'GoogleSheet', 'Email'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function sendInfo($data = []) 
    {
        $request = $this->controller->getRequest();

        if(empty($data)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
        $check_recaptcha = $this->ReCaptcha->check($token);
        if($check_recaptcha[CODE] != SUCCESS){
            return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
        }

        $form_code = !empty($data['form_code']) ? $data['form_code'] : null;
        if(empty($form_code)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_cau_hinh_ma_form')]);
        }    

        $form_info = TableRegistry::get('ContactsForm')->find()->where([
            'code' => $form_code, 
            'deleted' => 0
        ])->first();
        if(empty($form_info)){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'ma_form_duoc_cau_hinh_khong_ton_tai_tren_he_thong')
            ]);
        }

        // setting form
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $email_management = !empty($settings['email']['email_administrator']) ? $settings['email']['email_administrator'] : null;

        $send_email = !empty($form_info['send_email']) ? true : false;
        $template_email_code = !empty($form_info['template_email_code']) ? $form_info['template_email_code'] : null;
        $add_google_sheet = !empty($form_info['google_sheet_status']) ? true : false;
        $fields = !empty($form_info['fields']) ? json_decode($form_info['fields'], true) : [];
        if(empty($fields) || !is_array($fields)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_cau_hinh_cac_truong_cua_form')]);
        }

        $data_value = [];

        // add value traffic_source to contact
        $traffic_source = !empty($request->getCookie(TRAFFIC_SOURCE)) ? $request->getCookie(TRAFFIC_SOURCE) : 'Direct';

        foreach ($fields as $key => $field) {
            $code = !empty($field['code']) ? $field['code'] : null;
            if(empty($code)) continue;
            $data_value[$code] = !empty($data[$code]) ? strip_tags($data[$code]) : '';
        }

        $data_save = [
            'form_id' => !empty($form_info['id']) ? $form_info['id'] : null,
            'value' => !empty($data_value) ? json_encode($data_value) : null,
            'ip' => !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
            'tracking_source' => $traffic_source,
            'status' => 2,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode($data_value))
        ];

        $entity = TableRegistry::get('Contacts')->newEntity($data_save);
        $add_contact = TableRegistry::get('Contacts')->save($entity);
        $contact_id = !empty($add_contact->id) ? intval($add_contact->id) : null;
        if (empty($contact_id)){
            return $this->System->getResponse();
        }

        // send email for admin
        if($send_email && !empty($template_email_code) && !empty($email_management)) {
            $params_email = [
                'to_email' => $email_management,
                'code' => $template_email_code,
                'id_record' => $contact_id
            ];

            $send = $this->Email->send($params_email);        
        }

        // send another message (telegram, slack)
        $this->SendMessage->send(CONTACT, $contact_id);

        // add to google sheet
        if($add_google_sheet) {
            $this->GoogleSheet->appendData($contact_id);    
        }

        return $this->System->getResponse([
            CODE => SUCCESS, 
            MESSAGE => __d('template', 'gui_thong_tin_lien_he_thanh_cong')
        ]);     
    }   

}
